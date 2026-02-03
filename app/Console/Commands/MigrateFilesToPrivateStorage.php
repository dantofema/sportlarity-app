<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\Feedback;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateFilesToPrivateStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:migrate-to-private
                            {--dry-run : Show what would be migrated without actually migrating}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from public storage to private storage for security';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔒 File Migration to Private Storage');
        $this->info('=====================================');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No files will be moved');
            $this->newLine();
        }

        // Count files to migrate
        $avatarCount = User::whereNotNull('image')->count();
        $documentFileCount = Document::whereNotNull('file')->count();
        $documentImageCount = Document::whereNotNull('image')->count();
        $feedbackCount = Feedback::whereNotNull('file')->count();

        $totalCount = $avatarCount + $documentFileCount + $documentImageCount + $feedbackCount;

        $this->info('Files to migrate:');
        $this->line("  • User avatars: {$avatarCount}");
        $this->line("  • Document files: {$documentFileCount}");
        $this->line("  • Document images: {$documentImageCount}");
        $this->line("  • Feedback files: {$feedbackCount}");
        $this->line("  • Total: {$totalCount}");
        $this->newLine();

        if ($totalCount === 0) {
            $this->info('✅ No files to migrate.');

            return Command::SUCCESS;
        }

        if (! $force && ! $dryRun && ! $this->confirm('Do you want to proceed with the migration?', true)) {
            $this->warn('Migration cancelled.');

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('Starting migration...');
        $this->newLine();

        $stats = [
            'migrated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        // Migrate avatars
        if ($avatarCount > 0) {
            $this->info('📸 Migrating user avatars...');
            $stats = $this->migrateAvatars($dryRun, $stats);
        }

        // Migrate document files
        if ($documentFileCount > 0) {
            $this->info('📄 Migrating document files...');
            $stats = $this->migrateDocumentFiles($dryRun, $stats);
        }

        // Migrate document images
        if ($documentImageCount > 0) {
            $this->info('🖼️  Migrating document images...');
            $stats = $this->migrateDocumentImages($dryRun, $stats);
        }

        // Migrate feedback files
        if ($feedbackCount > 0) {
            $this->info('💬 Migrating feedback files...');
            $stats = $this->migrateFeedbackFiles($dryRun, $stats);
        }

        $this->newLine();
        $this->info('=====================================');
        $this->info('Migration Summary:');
        $this->line("  ✅ Migrated: {$stats['migrated']}");
        $this->line("  ⏭️  Skipped: {$stats['skipped']}");
        $this->line("  ❌ Errors: {$stats['errors']}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('This was a DRY RUN. Run without --dry-run to actually migrate files.');
        } else {
            $this->info('✅ Migration complete!');
        }

        return Command::SUCCESS;
    }

    private function migrateAvatars(bool $dryRun, array $stats): array
    {
        $users = User::whereNotNull('image')->get();

        foreach ($users as $user) {
            $oldPath = $user->image;

            // Skip if already in private storage
            if (str_starts_with((string) $oldPath, 'avatars/')) {
                $this->line("  ⏭️  Skipped (already private): {$user->name}");
                $stats['skipped']++;

                continue;
            }

            // Check if file exists in public storage
            if (! Storage::disk('public')->exists($oldPath)) {
                $this->warn("  ❌ File not found: {$oldPath} (User: {$user->name})");
                $stats['errors']++;

                continue;
            }

            // New path (keep original filename)
            $filename = basename((string) $oldPath);
            $newPath = "avatars/{$filename}";

            if ($dryRun) {
                $this->line("  📸 Would migrate: {$oldPath} → {$newPath}");
                $stats['migrated']++;
            } else {
                try {
                    // Copy file to private storage
                    $fileContents = Storage::disk('public')->get($oldPath);
                    Storage::disk('private_avatars')->put($filename, $fileContents);

                    // Update database
                    $user->update(['image' => $newPath]);

                    // Delete from public storage
                    Storage::disk('public')->delete($oldPath);

                    $this->line("  ✅ Migrated: {$user->name}");
                    $stats['migrated']++;
                } catch (Exception $e) {
                    $this->error("  ❌ Error migrating {$user->name}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            }
        }

        return $stats;
    }

    private function migrateDocumentFiles(bool $dryRun, array $stats): array
    {
        $documents = Document::whereNotNull('file')->get();

        foreach ($documents as $document) {
            $oldPath = $document->file;

            // Skip if already in private storage
            if (str_starts_with((string) $oldPath, 'documents/')) {
                $stats['skipped']++;

                continue;
            }

            // Check if file exists in public storage
            if (! Storage::disk('public')->exists($oldPath)) {
                $this->warn("  ❌ File not found: {$oldPath} (Document ID: {$document->id})");
                $stats['errors']++;

                continue;
            }

            // New path (keep original filename)
            $filename = basename((string) $oldPath);
            $newPath = "documents/{$filename}";

            if ($dryRun) {
                $this->line("  📄 Would migrate: {$oldPath} → {$newPath}");
                $stats['migrated']++;
            } else {
                try {
                    // Copy file to private storage
                    $fileContents = Storage::disk('public')->get($oldPath);
                    Storage::disk('private_documents')->put($filename, $fileContents);

                    // Update database
                    $document->update(['file' => $newPath]);

                    // Delete from public storage
                    Storage::disk('public')->delete($oldPath);

                    $this->line("  ✅ Migrated document ID: {$document->id}");
                    $stats['migrated']++;
                } catch (Exception $e) {
                    $this->error("  ❌ Error migrating document {$document->id}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            }
        }

        return $stats;
    }

    private function migrateDocumentImages(bool $dryRun, array $stats): array
    {
        $documents = Document::whereNotNull('image')->get();

        foreach ($documents as $document) {
            $oldPath = $document->image;

            // Skip if already in private storage
            if (str_starts_with((string) $oldPath, 'documents/')) {
                $stats['skipped']++;

                continue;
            }

            // Check if file exists in public storage
            if (! Storage::disk('public')->exists($oldPath)) {
                $this->warn("  ❌ Image not found: {$oldPath} (Document ID: {$document->id})");
                $stats['errors']++;

                continue;
            }

            // New path (keep original filename)
            $filename = basename((string) $oldPath);
            $newPath = "documents/{$filename}";

            if ($dryRun) {
                $this->line("  🖼️  Would migrate: {$oldPath} → {$newPath}");
                $stats['migrated']++;
            } else {
                try {
                    // Copy file to private storage
                    $fileContents = Storage::disk('public')->get($oldPath);
                    Storage::disk('private_documents')->put($filename, $fileContents);

                    // Update database
                    $document->update(['image' => $newPath]);

                    // Delete from public storage
                    Storage::disk('public')->delete($oldPath);

                    $this->line("  ✅ Migrated document image ID: {$document->id}");
                    $stats['migrated']++;
                } catch (Exception $e) {
                    $this->error("  ❌ Error migrating document image {$document->id}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            }
        }

        return $stats;
    }

    private function migrateFeedbackFiles(bool $dryRun, array $stats): array
    {
        $feedbacks = Feedback::whereNotNull('file')->get();

        foreach ($feedbacks as $feedback) {
            $oldPath = $feedback->file;

            // Skip if already in private storage
            if (str_starts_with((string) $oldPath, 'feedback/')) {
                $stats['skipped']++;

                continue;
            }

            // Check if file exists in public storage
            if (! Storage::disk('public')->exists($oldPath)) {
                $this->warn("  ❌ File not found: {$oldPath} (Feedback ID: {$feedback->id})");
                $stats['errors']++;

                continue;
            }

            // New path (keep original filename)
            $filename = basename((string) $oldPath);
            $newPath = "feedback/{$filename}";

            if ($dryRun) {
                $this->line("  💬 Would migrate: {$oldPath} → {$newPath}");
                $stats['migrated']++;
            } else {
                try {
                    // Copy file to private storage
                    $fileContents = Storage::disk('public')->get($oldPath);
                    Storage::disk('private_feedback')->put($filename, $fileContents);

                    // Update database
                    $feedback->update(['file' => $newPath]);

                    // Delete from public storage
                    Storage::disk('public')->delete($oldPath);

                    $this->line("  ✅ Migrated feedback ID: {$feedback->id}");
                    $stats['migrated']++;
                } catch (Exception $e) {
                    $this->error("  ❌ Error migrating feedback {$feedback->id}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            }
        }

        return $stats;
    }
}
