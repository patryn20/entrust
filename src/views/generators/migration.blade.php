{{ '<?php' }}

use Illuminate\Database\Migrations\Migration;

class EntrustSetupTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creates the roles table
        Schema::create('{{ $table_prefix }}roles', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->string('permissions');
            $table->timestamps();
        });

        // Creates the assigned_roles (Many-to-Many relation) table
        Schema::create('{{ $table_prefix }}assigned_roles', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('{{ $table_prefix }}user_id')->unsigned();
            $table->integer('{{ $table_prefix }}role_id')->unsigned();
            $table->foreign('{{ $table_prefix }}user_id')->references('id')->on('{{ $table_prefix }}users'); // assumes a users table
            $table->foreign('{{ $table_prefix }}role_id')->references('id')->on('{{ $table_prefix }}roles');
        });

        // Creates the permissions table
        Schema::create('{{ $table_prefix }}permissions', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('display_name');
            $table->timestamps();
        });

        // Creates the permission_role (Many-to-Many relation) table
        Schema::create('{{ $table_prefix }}permission_role', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('{{ $table_prefix }}permission_id')->unsigned();
            $table->integer('{{ $table_prefix }}role_id')->unsigned();
            $table->foreign('{{ $table_prefix }}permission_id')->references('id')->on('{{ $table_prefix }}permissions'); // assumes a users table
            $table->foreign('{{ $table_prefix }}role_id')->references('id')->on('{{ $table_prefix }}roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('{{ $table_prefix }}assigned_roles');
        Schema::drop('{{ $table_prefix }}permission_role');
        Schema::drop('{{ $table_prefix }}roles');
        Schema::drop('{{ $table_prefix }}permissions');
    }

}
