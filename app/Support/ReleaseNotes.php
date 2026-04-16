<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ReleaseNotes
{
    /**
     * @return array{version: string, branch: string, commit_hash: string, commit_subject: string}
     */
    public static function current(): array
    {
        $version = self::appVersion();
        $cacheKey = 'release_notes:'.$version;

        return Cache::rememberForever($cacheKey, function () use ($version): array {
            return [
                'version' => $version,
                'branch' => self::gitBranch(),
                'commit_hash' => self::gitCommitHash(),
                'commit_subject' => self::gitCommitSubject(),
            ];
        });
    }

    protected static function appVersion(): string
    {
        $versionFile = base_path('version.txt');
        if (! is_file($versionFile)) {
            return 'Dev (Local)';
        }

        $value = trim((string) file_get_contents($versionFile));

        return $value !== '' ? $value : 'Dev (Local)';
    }

    protected static function gitBranch(): string
    {
        return self::runGitCommand('rev-parse --abbrev-ref HEAD') ?: 'unknown';
    }

    protected static function gitCommitHash(): string
    {
        return self::runGitCommand('log -1 --pretty=format:%h') ?: 'n/a';
    }

    protected static function gitCommitSubject(): string
    {
        return self::runGitCommand('log -1 --pretty=format:%s') ?: 'No disponible';
    }

    protected static function runGitCommand(string $args): ?string
    {
        $repo = escapeshellarg(base_path());
        $command = "git -C {$repo} {$args} 2>/dev/null";
        $output = shell_exec($command);

        if (! is_string($output)) {
            return null;
        }

        $value = trim($output);

        return $value !== '' ? $value : null;
    }
}
