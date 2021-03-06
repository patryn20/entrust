<?php namespace Zizaco\Entrust;

use LaravelBook\Ardent\Ardent;

class EntrustRole extends Ardent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    protected $table_prefix = '';

    protected $class_prefix = '';

    /**
     * Ardent validation rules
     *
     * @var array
     */
    public static $rules = array(
      'name' => 'required|between:4,16'
    );


    public function __construct($attributes = array(), $exists = false)
    {
      $app = app();
      $table_prefix = $app['config']->get('entrust::table_prefix');
      $class_prefix = $app['config']->get('entrust::class_prefix');
      if (!empty($table_prefix)) {
        $this->table_prefix = $table_prefix;
        $this->table = $this->table_prefix . $this->table;
      }
      if (!empty($class_prefix)) {
        $this->class_prefix = $class_prefix;
      }
      parent::__construct($attributes, $exists);
    }

    /**
     * Many-to-Many relations with Users
     */
    public function users()
    {
        return $this->belongsToMany($this->class_prefix . 'User', $this->table_prefix . 'assigned_roles');
    }

    /**
     * Many-to-Many relations with Permission
     * named perms as permissions is already taken.
     */
    public function perms()
    {
        // To maintain backwards compatibility we'll catch the exception if the Permission table doesn't exist.
        // TODO remove in a future version
        try {
            return $this->belongsToMany($this->class_prefix . 'Permission');
        } catch(Execption $e) {}
    }

    /**
     * Before save should serialize permissions to save
     * as text into the database
     *
     * @param array $value
     */
    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = json_encode($value);
    }

    /**
     * When loading the object it should un-serialize permissions to be
     * usable again
     *
     * @param string $value
     * permissoins json
     */
    public function getPermissionsAttribute($value)
    {
        return (array)json_decode($value);
    }

    /**
     * Before delete all constrained foreign relations
     *
     * @param bool $forced
     * @return bool
     */
    public function beforeDelete( $forced = false )
    {
        try {
            \DB::table($this->table_prefix . 'assigned_roles')->where($this->table_prefix . 'role_id', $this->id)->delete();
            \DB::table($this->table_prefix . 'permission_' . $this->table_prefix. 'role')->where($this->table_prefix . 'role_id', $this->id)->delete();
        } catch(Execption $e) {}

        return true;
    }


    /**
     * Save permissions inputted
     * @param $inputPermissions
     */
    public function savePermissions($inputPermissions)
    {
        if(! empty($inputPermissions)) {
            $this->perms()->sync($inputPermissions);
        } else {
            $this->perms()->detach();
        }
    }

    /**
     * Attach permission to current role
     * @param $permission
     */
    public function attachPermission( $permission )
    {
        if( is_object($permission))
            $permission = $permission->getKey();

        if( is_array($permission))
            $permission = $permission['id'];

        $this->perms()->attach( $permission );
    }

    /**
     * Detach permission form current role
     * @param $permission
     */
    public function detachPermission( $permission )
    {
        if( is_object($permission))
            $permission = $permission->getKey();

        if( is_array($permission))
            $permission = $permission['id'];

        $this->perms()->detach( $permission );
    }

}
