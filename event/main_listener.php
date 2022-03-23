<?php
/**
 *
 * Required Edit Reason extension for the phpBB Forum Software package
 *
 * @copyright (c) 2022, Kailey Snay, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileysnay\requireeditreason\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Required Edit Reason event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth          $auth
	 * @param \phpbb\language\language  $language
	 * @param \phpbb\user               $user
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\language\language $language, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->language = $language;
		$this->user = $user;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.posting_modify_submission_errors'	=> 'posting_modify_submission_errors',

			'core.user_setup'	=> 'user_setup',
		];
	}

	public function posting_modify_submission_errors($event)
	{
		$error = $event['error'];

		if ($event['mode'] == 'edit' && !$event['post_data']['post_edit_reason'] && $this->auth->acl_get('m_edit', (int) $event['forum_id']) && (int) $event['post_data']['poster_id'] != $this->user->data['user_id'])
		{
			$error[] = $this->language->lang('EDIT_REASON_REQUIRED');
		}

		$event['error'] = $error;
	}

	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'kaileysnay/requireeditreason',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}
}
