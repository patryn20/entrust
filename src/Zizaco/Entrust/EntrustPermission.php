<?php namespace Zizaco\Entrust;

use LaravelBook\Ardent\Ardent;

class EntrustPermission extends Ardent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    protected $table_prefix = '';

    /**
     * Ardent validation rules
     *
     * @var array
     */
    public static $rules = array(
      'name' => 'required|between:4,32',
      'display_name' => 'required|between:4,32'
    );

    public function __construct($attributes = array(), $exists = false)
    {
      $app = app();
      if (!empty($app['config']->get('entrust::table_prefix'))) {
        $this->table_prefix = $app['config']->get('entrust::table_prefix');
        $this->table = $this->table_prefix . $this->table;
      }
      parent::__construct($attributes, $exists);
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
            \DB::table($this->table_prefix . 'permission_role')->where($this->table_prefix . 'permission_id', $this->id)->delete();
        } catch(Execption $e) {}

        return true;
    }

}
