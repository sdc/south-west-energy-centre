<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * EnergyCentreCourses Model
 */
class EnergyCentreCoursesModelEnergyCentreCourses extends JModelItem
{
        /**
         * @var string msg
         */
        protected $msg;
 
        /**
         * Get the message
         * @return string The message to be displayed to the user
         */
        public function getMsg() 
        {
                if (!isset($this->msg)) 
                {
                        $id = JRequest::getInt('id');
                        switch ($id) 
                        {
                        case 2:
                                $this->msg = 'Whoop';
                        break;
                        default:
                        case 1:
                                $this->msg = 'Cheese';
                        break;
                        }
                }
                return $this->msg;
        }
}

