<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Match;
use App\Models\CompetitionTeam;
use Illuminate\Support\Collection;

class FixtureGeneratorService
{
    /**
     * Generate round-robin fixtures for a competition
     */
    public function generateRoundRobin(Competition $competition, array $options = []): array
    {
        $teams = CompetitionTeam::where('competition_id', $competition->id)
            ->pluck('club_id')
            ->toArray();

        if (count($teams) < 2) {
            throw new \Exception('Need at least 2 teams to generate fixtures');
        }

        $homeAndAway = $options['home_and_away'] ?? true;
        $startDate = $options['start_date'] ?? now()->addWeek();
        $matchDays = $options['match_days'] ?? ['Saturday', 'Sunday'];
        $defaultTime = $options['default_time'] ?? '15:00';

        // If odd number of teams, add a "bye" team
        if (count($teams) % 2 !== 0) {
            $teams[] = null; // null represents a bye
        }

        $numTeams = count($teams);
        $numRounds = $numTeams - 1;
        $matchesPerRound = $numTeams / 2;

        $fixtures = [];
        $currentDate = $startDate->copy();

        // Generate first half of season
        for ($round = 0; $round < $numRounds; $round++) {
            $roundFixtures = $this->generateRound($teams, $round, $numTeams);

            foreach ($roundFixtures as $fixture) {
                if ($fixture['home'] !== null && $fixture['away'] !== null) {
                    $matchDate = $this->getNextMatchDay($currentDate, $matchDays);

                    $fixtures[] = [
                        'competition_id' => $competition->id,
                        'home_club_id' => $fixture['home'],
                        'away_club_id' => $fixture['away'],
                        'match_date' => $matchDate,
                        'match_time' => $defaultTime,
                        'round' => $round + 1,
                        'status' => 'scheduled',
                    ];
                }
            }

            $currentDate = $currentDate->addWeek();
        }

        // Generate second half (reverse fixtures) if home and away
        if ($homeAndAway) {
            $firstHalfFixtures = $fixtures;
            foreach ($firstHalfFixtures as $fixture) {
                $matchDate = $this->getNextMatchDay($currentDate, $matchDays);

                $fixtures[] = [
                    'competition_id' => $competition->id,
                    'home_club_id' => $fixture['away_club_id'],
                    'away_club_id' => $fixture['home_club_id'],
                    'match_date' => $matchDate,
                    'match_time' => $defaultTime,
                    'round' => $fixture['round'] + $numRounds,
                    'status' => 'scheduled',
                ];

                if (count($fixtures) % $matchesPerRound === 0) {
                    $currentDate = $currentDate->addWeek();
                }
            }
        }

        return $fixtures;
    }

    /**
     * Save generated fixtures to database
     */
    public function saveFixtures(array $fixtures): Collection
    {
        $matches = collect();

        foreach ($fixtures as $fixture) {
            $match = Match::create($fixture);
            $matches->push($match);
        }

        return $matches;
    }

    /**
     * Generate knockout tournament bracket
     */
    public function generateKnockout(Competition $competition, array $options = []): array
    {
        $teams = CompetitionTeam::where('competition_id', $competition->id)
            ->pluck('club_id')
            ->toArray();

        $numTeams = count($teams);

        // Must be power of 2
        if (($numTeams & ($numTeams - 1)) !== 0) {
            throw new \Exception('Knockout tournaments require power of 2 teams (4, 8, 16, 32, etc.)');
        }

        // Shuffle teams for random draw
        if ($options['random_draw'] ?? true) {
            shuffle($teams);
        }

        $startDate = $options['start_date'] ?? now()->addWeek();
        $defaultTime = $options['default_time'] ?? '15:00';

        $fixtures = [];
        $round = 1;
        $roundName = $this->getRoundName($numTeams);

        // First round matches
        for ($i = 0; $i < $numTeams; $i += 2) {
            $fixtures[] = [
                'competition_id' => $competition->id,
                'home_club_id' => $teams[$i],
                'away_club_id' => $teams[$i + 1],
                'match_date' => $startDate->copy(),
                'match_time' => $defaultTime,
                'round' => $round,
                'round_name' => $roundName,
                'status' => 'scheduled',
            ];
        }

        return $fixtures;
    }

    /**
     * Generate group stage fixtures
     */
    public function generateGroupStage(Competition $competition, int $numGroups = 4, array $options = []): array
    {
        $teams = CompetitionTeam::where('competition_id', $competition->id)
            ->pluck('club_id')
            ->toArray();

        $teamsPerGroup = count($teams) / $numGroups;

        if (count($teams) % $numGroups !== 0) {
            throw new \Exception("Cannot divide {count($teams)} teams into {$numGroups} equal groups");
        }

        // Shuffle and distribute teams into groups
        shuffle($teams);
        $groups = array_chunk($teams, $teamsPerGroup);

        $allFixtures = [];

        foreach ($groups as $groupIndex => $groupTeams) {
            $groupFixtures = $this->generateRoundRobinForGroup(
                $competition->id,
                $groupTeams,
                chr(65 + $groupIndex), // A, B, C, D...
                $options
            );

            $allFixtures = array_merge($allFixtures, $groupFixtures);
        }

        return $allFixtures;
    }

    private function generateRound(array $teams, int $round, int $numTeams): array
    {
        $fixtures = [];

        for ($i = 0; $i < $numTeams / 2; $i++) {
            $home = ($round + $i) % ($numTeams - 1);
            $away = ($numTeams - 1 - $i + $round) % ($numTeams - 1);

            // Last team stays fixed
            if ($i === 0) {
                $away = $numTeams - 1;
            }

            $fixtures[] = [
                'home' => $teams[$home],
                'away' => $teams[$away],
            ];
        }

        return $fixtures;
    }

    private function generateRoundRobinForGroup(int $competitionId, array $teams, string $group, array $options): array
    {
        $startDate = $options['start_date'] ?? now()->addWeek();
        $defaultTime = $options['default_time'] ?? '15:00';

        if (count($teams) % 2 !== 0) {
            $teams[] = null;
        }

        $numTeams = count($teams);
        $numRounds = $numTeams - 1;
        $fixtures = [];
        $currentDate = $startDate->copy();

        for ($round = 0; $round < $numRounds; $round++) {
            $roundFixtures = $this->generateRound($teams, $round, $numTeams);

            foreach ($roundFixtures as $fixture) {
                if ($fixture['home'] !== null && $fixture['away'] !== null) {
                    $fixtures[] = [
                        'competition_id' => $competitionId,
                        'home_club_id' => $fixture['home'],
                        'away_club_id' => $fixture['away'],
                        'match_date' => $currentDate->copy(),
                        'match_time' => $defaultTime,
                        'round' => $round + 1,
                        'group' => $group,
                        'status' => 'scheduled',
                    ];
                }
            }

            $currentDate = $currentDate->addWeek();
        }

        return $fixtures;
    }

    private function getNextMatchDay(\Carbon\Carbon $date, array $matchDays): \Carbon\Carbon
    {
        while (!in_array($date->format('l'), $matchDays)) {
            $date->addDay();
        }

        return $date->copy();
    }

    private function getRoundName(int $totalTeams): string
    {
        return match ($totalTeams) {
            2 => 'Final',
            4 => 'Semi-Final',
            8 => 'Quarter-Final',
            16 => 'Round of 16',
            32 => 'Round of 32',
            64 => 'Round of 64',
            default => "Round of {$totalTeams}",
        };
    }
}
