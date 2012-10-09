<?php

class Application_Model_DbTable_Group extends Zend_Db_Table_Abstract
{

    protected $_name = 'fb_groups';

    /**
    * Class instance
    * @var Application_Db_Table_Group
    */
    private static $_instance;


    /**
     * Function to get all groups
     */
    public function getAll($userId = null)
    {
        $select = $this->select();
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        //echo $select;exit;
        return $this->fetchAll($select);


    }


    public function getAllActiveGroups($userId = null)
    {
        $select = $this->select();
        $select->where('Status = 1');
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        return $this->fetchAll($select);
    }


    public function search($groupName, $userId = null)
    {
        $select = $this->select();
        
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);
        
        $select->where('Name Like ?', '%'.$groupName.'%');
        
        return $this->fetchAll($select);
    }

    /**
     * Function to save a group
     * @param <array> $data
     */
    public function saveGroup($data, $userId = null)
    {
        $select = $this->select();
        $select->where('GroupId = ?',$data['id']);
        
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);

        $row = $this->fetchRow($select);

         //if it does not then create it
        if(!$row) {
             $row = $this->createRow();
             $row->Status  = 1;
        }
        $row->GroupId = $data['id'];
        $row->Email   = $data['email'];
        $row->Name    = $data['name'];
        $row->UserId  = $userId;
        $row->save();
        
    }

    /**
     * Function to to switch group status
     * @param <int> $id
     */
    public function switchGroup($id, $userId = null)
    {
        $select = $this->select();
        $select->where('id = ?', $id);
        
        //if user id is set
        if(isset ($userId))
            $select->where('UserId = ?', $userId);

        $row = $this->fetchRow($select);

        if((int)$row->Status == 0)
            $row->Status = 1;
        else
            $row->Status = 0;

        $row->save();

    }

      /**
     * Function to to switch groups' status
     * @param <array> $params
     */
    public function switchGroups($params, $userId= null)
    {
        //Zend_Debug::dump($params);exit;
        foreach($params as $group=>$value) {

            // if the param is not submit
            if($group != 'deactivate') {

                $select = $this->select();
                $select->where('id = ?', $group);
                //if user id is set
                if(isset ($userId))
                   $select->where('UserId = ?', $userId);

                $row = $this->fetchRow($select);

                // switch status
                if((int)$row->Status == 0)
                    $row->Status = 1;
                else
                    $row->Status = 0;

                $row->save();

            }

        }

    }



    public function deleteGroup($groupId, $userId)
    {
        $select = $this->select();
        $select->where('GroupId = ?', $groupId);
        $select->where('UserId = ?', $userId);
       
        $where=array('GroupId = ?'=> $groupId,'UserId = ?'=>$userId);

        $this->delete($where);
        
        

    }




  /**
   * Fuction to get class instance
   * @return Application_Db_Table_Config
   */
   public static function getInstance()
   {
        //return new self();
        if( !isset(self::$_instance) ){
            $instance = new self();
            self::$_instance = $instance;
        }
            return self::$_instance;
   }






}

