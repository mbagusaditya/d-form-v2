<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $this->makeUserIdNullable('form_answers');

        if (Schema::hasTable('email_logs')) {
            $this->makeUserIdNullable('email_logs');
        }

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement(
                'CREATE UNIQUE INDEX form_answers_active_guest_email_form_unique '
                .'ON form_answers (form_id, invited_email) '
                ."WHERE user_id IS NULL "
                ."AND invited_email IS NOT NULL "
                ."AND invited_email != '' "
                ."AND COALESCE(review_status, '') != 'rejected' "
                ."AND NOT (registration_role = 'member' AND member_confirmation_status IN ('rejected', 'expired'))"
            );
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('DROP INDEX IF EXISTS form_answers_active_guest_email_form_unique');
        }

        if (Schema::hasTable('email_logs')) {
            $this->makeUserIdRequired('email_logs');
        }

        $this->makeUserIdRequired('form_answers');
    }

    private function makeUserIdNullable(string $table): void
    {
        if (! Schema::hasColumn($table, 'user_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['user_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            if ($table === 'form_answers') {
                DB::statement('DROP INDEX IF EXISTS form_answers_active_user_form_unique');
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('user_id');
            });
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->uuid('user_id')->nullable();
                $blueprint->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });

            if ($table === 'form_answers') {
                DB::statement(
                    'CREATE UNIQUE INDEX form_answers_active_user_form_unique '
                    .'ON form_answers (user_id, form_id) '
                    ."WHERE user_id IS NOT NULL "
                    ."AND COALESCE(review_status, '') != 'rejected' "
                    ."AND NOT (registration_role = 'member' AND member_confirmation_status IN ('rejected', 'expired'))"
                );
            }

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE {$table} MODIFY user_id CHAR(36) NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE {$table} ALTER COLUMN user_id DROP NOT NULL");
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    private function makeUserIdRequired(string $table): void
    {
        if (! Schema::hasColumn($table, 'user_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->dropForeign(['user_id']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            if ($table === 'form_answers') {
                DB::statement('DROP INDEX IF EXISTS form_answers_active_user_form_unique');
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('user_id');
            });
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->uuid('user_id');
                $blueprint->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            });

            if ($table === 'form_answers') {
                DB::statement(
                    'CREATE UNIQUE INDEX form_answers_active_user_form_unique '
                    .'ON form_answers (user_id, form_id) '
                    ."WHERE COALESCE(review_status, '') != 'rejected' "
                    ."AND NOT (registration_role = 'member' AND member_confirmation_status IN ('rejected', 'expired'))"
                );
            }

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE {$table} MODIFY user_id CHAR(36) NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE {$table} ALTER COLUMN user_id SET NOT NULL");
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};
