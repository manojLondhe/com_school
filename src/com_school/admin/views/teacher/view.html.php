<?php

/**
 * @version    CVS: 1.0.4
 * @package    Com_School
 * @author     Manoj L <manoj_l@techjoomla.com>
 * @copyright  Copyright (C) 2017. All rights reserved.
 * @license    Manoj
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

/**
 * View to edit
 *
 * @since  1.6
 */
class SchoolViewTeacher extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = SchoolHelper::getActions();

		JToolBarHelper::title(Text::_('COM_SCHOOL_TITLE_TEACHER'), 'teacher.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('teacher.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('teacher.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			JToolBarHelper::custom('teacher.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolBarHelper::custom('teacher.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		// Button for version control
		if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit')) {
			JToolbarHelper::versions('com_school.teacher', $this->item->id);
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('teacher.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('teacher.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
