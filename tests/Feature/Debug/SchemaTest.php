<?php

namespace Tests\Feature\Debug;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_columns()
    {
        $columns = Schema::getColumnListing('users');
        dump($columns);
        
        $this->assertTrue(Schema::hasColumn('users', 'active_mission_set_id'), 'active_mission_set_id missing');
        $this->assertTrue(Schema::hasColumn('users', 'mission_started_at'), 'mission_started_at missing');
    }
    
    public function test_user_model_fillable()
    {
        $user = new \App\Models\User();
        dump($user->getFillable());
        
        $this->assertTrue(in_array('active_mission_set_id', $user->getFillable()), 'active_mission_set_id not in fillable');
    }
}
