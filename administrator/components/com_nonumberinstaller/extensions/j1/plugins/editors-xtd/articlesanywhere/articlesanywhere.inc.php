<?php
/**
 * Popup page
 * Displays a list with modules
 *
 * @package         Articles Anywhere
 * @version         2.4.6
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
if ($user->get('guest')) {
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
$parameters = NNParameters::getInstance();
$params = $parameters->getPluginParams('articlesanywhere');

$app = JFactory::getApplication();
if ($app->isSite()) {
	if (!$params->enable_frontend) {
		JError::raiseError(403, JText::_("ALERTNOTAUTH"));
	}
}

$class = new plgButtonArticlesAnywherePopup;
$class->render($params);

class plgButtonArticlesAnywherePopup
{
	function render(&$params)
	{
		$app = JFactory::getApplication();

		// load the admin language file
		$lang = JFactory::getLanguage();
		if ($lang->getTag() != 'en-GB') {
			// Loads English language file as fallback (for undefined stuff in other language file)
			$lang->load('plg_system_articlesanywhere', JPATH_ADMINISTRATOR, 'en-GB');
		}
		$lang->load('plg_system_articlesanywhere', JPATH_ADMINISTRATOR, null, 1);
		// load the content language file
		$lang->load('com_content', JPATH_ADMINISTRATOR);

		require_once JPATH_ADMINISTRATOR . '/components/com_content/helper.php';

		$content_type = 'core';
		$k2 = 0;

		$db = JFactory::getDBO();
		$client = JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$filter = null;

		// Get some variables from the request
		$sectionid = JRequest::getVar('sectionid', -1, '', 'int');
		$filter_order = $app->getUserStateFromRequest('articlesanywhere_filter_order', 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('articlesanywhere_filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_featured = $app->getUserStateFromRequest('articlesanywhere_filter_featured', 'filter_featured', '', 'int');
		$filter_section = $app->getUserStateFromRequest('articlesanywhere_filter_section', 'filter_section', -1, 'int');
		$filter_category = $app->getUserStateFromRequest('articlesanywhere_filter_category', 'filter_category', 0, 'int');
		$filter_author = $app->getUserStateFromRequest('articlesanywhere_filter_author', 'filter_author', 0, 'int');
		$filter_state = $app->getUserStateFromRequest('articlesanywhere_filter_state', 'filter_state', '', 'word');
		$search = $app->getUserStateFromRequest('articlesanywhere_search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest('articlesanywhere_limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$lists = array();

		// search filter
		$lists['search'] = $search;

		// table ordering
			if ($filter_order == 'featured') {
				$filter_order = 'ordering';
				$filter_order_Dir = '';
			}

		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

			/* FILTERS */

			if ($filter_section >= 0) {
				$filter = ' WHERE cc.section = ' . (int) $filter_section;
			}

			// get list of categories for dropdown filter
			$query = 'SELECT cc.id AS value, cc.title AS text, section
				FROM #__categories AS cc
				INNER JOIN #__sections AS s ON s.id = cc.section
				' . $filter . '
				ORDER BY s.ordering, cc.ordering
				';

			$categories[] = JHtml::_('select.option', '0', '- ' . JText::_('Select Category') . ' -');
			$db->setQuery($query);
			$categories = array_merge($categories, $db->loadObjectList());
			$lists['categories'] = JHtml::_('select.genericlist', $categories, 'filter_category', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_category);

			// get list of sections for dropdown filter
			$javascript = 'onchange="this.form.submit();"';
			$lists['sections'] = JHtml::_('list.section', 'filter_section', $filter_section, $javascript);

			// get list of Authors for dropdown filter
			$query = 'SELECT c.created_by, u.name
				FROM #__content AS c
				LEFT JOIN #__users AS u ON u.id = c.created_by
				WHERE c.state <> -1
				AND c.state <> -2
				GROUP BY u.id
				ORDER BY u.id DESC
				';
			$authors[] = JHtml::_('select.option', '0', '- ' . JText::_('Select Author') . ' -', 'created_by', 'name');
			$db->setQuery($query);
			$authors = array_merge($authors, $db->loadObjectList());
			$lists['authors'] = JHtml::_('select.genericlist', $authors, 'filter_author', 'class="inputbox" size="1" onchange="this.form.submit( );"', 'created_by', 'name', $filter_author);

			// state filter
			$lists['state'] = JHtml::_('grid.state', $filter_state, 'Published', 'Unpublished', 'Archived');

			/* ITEMS */
			$where = array();
			$where[] = 'c.state != -2';

			$section = new stdClass;
			$section->title = 'All Articles';
			$section->id = 0;

			/*
			 * Add the filter specific information to the where clause
			 */
			// Section filter
			if ($filter_section >= 0) {
				$where[] = 'c.sectionid = ' . (int) $filter_section;
			}
			// Category filter
			if ($filter_category > 0) {
				$where[] = 'c.catid = ' . (int) $filter_category;
			}
			// Author filter
			if ($filter_author > 0) {
				$where[] = 'c.created_by = ' . (int) $filter_author;
			}
			// Content state filter
			if ($filter_state) {
				if ($filter_state == 'P') {
					$where[] = 'c.state = 1';
				} else {
					if ($filter_state == 'U') {
						$where[] = 'c.state = 0';
					} else if ($filter_state == 'A') {
						$where[] = 'c.state = -1';
					} else {
						$where[] = 'c.state != -2';
					}
				}
			}
			// Keyword filter
			if ($search) {
				$where[] = '(LOWER( c.title ) LIKE ' . $db->quote('%' . $db->getEscaped($search, true) . '%', false)
					. ' OR c.id = ' . (int) $search . ' )';
			}

			// Build the where clause of the content record query
			$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

			// Get the total number of records
			$query = 'SELECT COUNT( * )
				FROM #__content AS c
				LEFT JOIN #__categories AS cc ON cc.id = c.catid
				LEFT JOIN #__sections AS s ON s.id = c.sectionid
				' . $where;
			$db->setQuery($query);
			$total = $db->loadResult();

			// Create the pagination object
			jimport('joomla.html.pagination');
			$page = new JPagination($total, $limitstart, $limit);

			if ($filter_order == 'ordering') {
				$order = ' ORDER BY section, category, ordering ' . $filter_order_Dir;
			} else {
				$order = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ', section, category, ordering';
			}

			// Get the articles
			$query = 'SELECT c.*, c.state as published, g.name AS accesslevel, cc.title AS category, u.name AS editor, f.content_id AS frontpage, s.title AS section, v.name AS author
				FROM #__content AS c
				LEFT JOIN #__categories AS cc ON cc.id = c.catid
				LEFT JOIN #__sections AS s ON s.id = c.sectionid
				LEFT JOIN #__groups AS g ON g.id = c.access
				LEFT JOIN #__users AS u ON u.id = c.checked_out
				LEFT JOIN #__users AS v ON v.id = c.created_by
				LEFT JOIN #__content_frontpage AS f ON f.content_id = c.id
				' . $where
				. $order;
			$db->setQuery($query, $page->limitstart, $page->limit);
			$rows = $db->loadObjectList();

			// If there is a database query error, throw a HTTP 500 and exit
			if ($db->getErrorNum()) {
				JError::raiseError(500, $db->stderr());
				return false;
			}

		$this->outputHTML($params, $rows, $client, $page, $lists, $k2);
	}

	function outputHTML(&$params, &$rows, &$client, &$page, &$lists, $k2 = 0)
	{
		$app = JFactory::getApplication();

		JHtml::_('behavior.tooltip');

		$plugin_tag = explode(',', $params->article_tag);
		$plugin_tag = trim($plugin_tag['0']);

		$content_type = 'core';

		if (!empty($_POST)) {
			foreach ($params as $key => $val) {
				if (array_key_exists($key, $_POST)) {
					$params->$key = $_POST[$key];
				} else {
					$params->$key = 0;
				}
			}
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/versions.php';
		$version = NoNumberVersions::getXMLVersion(null, null, null, 1);

		// Add scripts and styles
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/nnframework/css/popup.css' . $version);
		$script = "
			function articlesanywhere_jInsertEditorText( id ) {
				var f = document.getElementById( 'adminForm' );
				var str = '';

				if ( f.data_title_enable.checked ) {
					str += ' {title}';
				}

				if ( f.data_text_enable.checked ) {
					var tag = f.data_text_type.options[f.data_text_type.selectedIndex].value.trim();
					var text_length = parseInt( f.data_text_length.value.trim() );
					if ( text_length && text_length != 0 ) {
						tag += ':'+text_length;
					}
					if ( f.data_text_strip.checked ) {
						tag += ':strip';
					}
					str += ' {'+tag+'}';
				}

				if ( f.data_readmore_enable.checked ) {
					var tag = 'readmore';
					var readmore_text = f.data_readmore_text.value.trim();
					var readmore_class = f.data_readmore_class.value.trim();
					if ( readmore_text ) {
						tag += ':'+readmore_text;
					}
					if ( readmore_class && readmore_class != 'readon' ) {
						if ( !readmore_text ) {
							tag += ':';
						}
						tag += '|'+readmore_class;
					}
					str += ' {'+tag+'}';
				}

				if ( f.data_id_enable.checked ) {
					str += ' {id}';
				}


				str = '{" . $plugin_tag . " " . ($content_type == 'k2' ? 'k2:' : '') . "'+id+'}'+str.trim()+'{/" . $plugin_tag . "}';

				window.parent.jInsertEditorText( str, '" . JRequest::getString('name', 'text') . "' );
				window.parent.document.getElementById( 'sbox-window' ).close();
			}

		";
		$document->addScriptDeclaration($script);
		?>
	<div style="margin: 0 10px;">
		<form action="" method="post" name="adminForm" id="adminForm">
			<fieldset>
				<div style="float: left">
					<h1><?php echo JText::_('ARTICLES_ANYWHERE'); ?></h1>
				</div>
				<div style="float: right">
					<div class="button2-left">
						<div class="blank hasicon cancel">
							<a rel="" onclick="window.parent.document.getElementById( 'sbox-window' ).close();" href="javascript://"
								title="<?php echo JText::_('Cancel') ?>"><?php echo JText::_('Cancel') ?></a>
						</div>
					</div>
				</div>
			</fieldset>
			<p><?php
				echo JText::_('AA_CLICK_ON_ONE_OF_THE_ARTICLE_LINKS');
				if ($app->isAdmin()) {
					$link = JURI::base(true) . '/index.php?option=com_plugins&client=site&filter_type=system&search=articles%20anywhere';
					echo '<br />' . html_entity_decode(JText::sprintf('AA_MORE_SYNTAX_HELP', $link), ENT_COMPAT, 'UTF-8');
				}
				?></p>

			<div style="clear:both;"></div>
			<table class="adminform" cellspacing="2" style="width:auto;float:left;margin-right:10px;">
				<tr>
					<th colspan="3">
						<label class="hasTip"
							title="<?php echo JText::_('AA_TITLE_TAG') . '::' . JText::_('AA_TITLE_TAG_DESC'); ?>">
							<input type="checkbox" name="data_title_enable"
								id="data_title_enable" <?php if ($params->data_title_enable) {
								echo 'checked="checked"';
							} ?> />
							<?php echo JText::_('Title'); ?>
						</label>
					</th>
				</tr>
				<tr>
					<th>
						<label class="hasTip"
							title="<?php echo JText::_('AA_TEXT_TAG') . '::' . JText::_('AA_TEXT_TAG_DESC'); ?>">
							<input type="checkbox" name="data_text_enable"
								id="data_text_enable" <?php if ($params->data_text_enable) {
								echo 'checked="checked"';
							} ?> />
						</label>
						<label class="hasTip"
							title="<?php echo JText::_('AA_TEXT_TYPE') . '::' . JText::_('AA_TEXT_TYPE_DESC'); ?>">
							<select name="data_text_type" class="inputbox">
								<option value="text"<?php if ($params->data_text_type == 'text') {
									echo 'selected="selected"';
								} ?>>
									<?php echo JText::_('AA_ALL_TEXT'); ?></option>
								<option value="introtext"<?php if ($params->data_text_type == 'introtext') {
									echo 'selected="selected"';
								} ?>>
									<?php echo JText::_('AA_INTRO_TEXT'); ?></option>
								<option value="fulltext"<?php if ($params->data_text_type == 'fulltext') {
									echo 'selected="selected"';
								} ?>>
									<?php echo JText::_('AA_FULL_TEXT'); ?></option>
							</select>
						</label>
					</th>
					<td>
						<label class="hasTip"
							title="<?php echo JText::_('AA_MAXIMUM_TEXT_LENGTH') . '::' . JText::_('AA_MAXIMUM_TEXT_LENGTH_DESC'); ?>">
							<?php echo JText::_('AA_MAXIMUM_TEXT_LENGTH'); ?>:
							<input type="text" class="text_area" name="data_text_length" id="data_text_length"
								value="<?php echo $params->data_text_length; ?>" size="4" style="text-align: right;" />
						</label>
					</td>
					<td>
						<label class="hasTip"
							title="<?php echo JText::_('AA_STRIP_HTML_TAGS') . '::' . JText::_('AA_STRIP_HTML_TAGS_DESC'); ?>">
							<input type="checkbox" name="data_text_strip"
								id="data_text_strip" <?php if ($params->data_text_strip) {
								echo 'checked="checked"';
							} ?> />
							<?php echo JText::_('AA_STRIP_HTML_TAGS'); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th>
						<label class="hasTip"
							title="<?php echo JText::_('AA_READMORE_TAG') . '::' . JText::_('AA_READMORE_TAG_DESC'); ?>">
							<input type="checkbox" name="data_readmore_enable"
								id="data_readmore_enable" <?php if ($params->data_readmore_enable) {
								echo 'checked="checked"';
							} ?> />
							<?php echo JText::_('AA_READMORE_LINK'); ?>
						</label>
					</th>
					<td>
						<label class="hasTip"
							title="<?php echo JText::_('AA_READMORE_TEXT') . '::' . JText::_('AA_READMORE_TEXT_DESC'); ?>">
							<?php echo JText::_('AA_READMORE_TEXT'); ?>:
							<input type="text" class="text_area" name="data_readmore_text" id="data_readmore_text"
								value="<?php echo $params->data_readmore_text; ?>" />
						</label>
					</td>
					<td>
						<label class="hasTip"
							title="<?php echo JText::_('AA_CLASSNAME') . '::' . JText::_('AA_CLASSNAME_DESC'); ?>">
							<?php echo JText::_('AA_CLASSNAME'); ?>:
							<input type="text" class="text_area" name="data_readmore_class" id="data_readmore_class"
								value="<?php echo $params->data_readmore_class; ?>" />
						</label>
					</td>
				</tr>
				<tr>
					<th colspan="3">
						<label class="hasTip"
							title="<?php echo JText::_('AA_ID_TAG') . '::' . JText::_('AA_ID_TAG_DESC'); ?>">
							<input type="checkbox" name="data_id_enable"
								id="data_id_enable" <?php if ($params->data_id_enable) {
								echo 'checked="checked"';
							} ?> />
							<?php echo JText::_('ID'); ?>
						</label>
					</th>
				</tr>
			</table>


			<div style="clear:both;"></div>

			<?php
				$this->outputTableCore($rows, $client, $page, $lists);
			?>

			<input type="hidden" name="name" value="<?php echo JRequest::getCmd('name', 'text'); ?>" />
			<input type="hidden" name="client" value="<?php echo $client->id; ?>" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
	</div>
	<?php
	}


	function outputTableCore(&$rows, &$client, &$page, &$lists)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$config = JFactory::getConfig();
		$now = JFactory::getDate();
		$nullDate = $db->getNullDate();
		?>
	<table class="adminform" cellspacing="1">
		<tbody>
			<tr>
				<td>
					<?php echo JText::_('Filter'); ?>:
					<input type="text" name="search" id="search" value="<?php echo $lists['search']; ?>"
						class="text_area" onchange="this.form.submit();"
						title="<?php echo JText::_('Filter by title or enter article ID'); ?>" />
					<button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
					<button onclick="
								document.getElementById( 'search' ).value='';
								document.getElementById( 'filter_section' ).value='-1';
								document.getElementById( 'filter_category' ).value='0';
								document.getElementById( 'filter_author' ).value='0';
								document.getElementById( 'filter_state' ).value='';
								this.form.submit();"><?php echo JText::_('Reset'); ?></button>
				</td>
				<td style="text-align:right;">
					<?php
					echo $lists['sections'];
					echo $lists['categories'];
					echo $lists['authors'];
					echo $lists['state'];
					?>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="adminlist adminform" cellspacing="1">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_('Num'); ?>
				</th>
				<th width="1%" class="title">
					<?php echo JHtml::_('grid.sort', 'ID', 'id', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'Title', 'title', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'Alias', 'alias', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Published', 'published', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th nowrap="nowrap" width="1%">
					<?php echo JHtml::_('grid.sort', 'Front Page', 'frontpage', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="8%">
					<?php echo JHtml::_('grid.sort', 'Order', 'ordering', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<?php
				if ($client->id == 0) {
					?>
					<th nowrap="nowrap" width="7%">
						<?php echo JHtml::_('grid.sort', 'Access', 'accesslevel', @$lists['order_Dir'], @$lists['order']); ?>
					</th>
					<?php
				}
				?>
				<th class="title" width="8%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Section', 'section', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title" width="8%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Category', 'category', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title" width="8%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'Author', 'author', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th align="center" width="10">
					<?php echo JHtml::_('grid.sort', 'Date', 'created', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th align="center" width="10">
					<?php echo JHtml::_('grid.sort', 'Hits', 'hits', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo ($client->id == 0) ? '13' : '12'; ?>">
					<?php
					$pagination = STR_REPLACE('index.php?', 'plugins/editors-xtd/articlesanywhere/elements/articlesanywhere.page.php?name=' . JRequest::getCmd('name', 'text') . '&', $page->getListFooter());
					$pagination = STR_REPLACE('index.php', 'plugins/editors-xtd/articlesanywhere/elements/articlesanywhere.page.php?name=' . JRequest::getCmd('name', 'text'), $pagination);
					echo $pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$i = 0;
			$k = 0;
			foreach ($rows as $row) {
				$publish_up = JFactory::getDate($row->publish_up);
				$publish_down = JFactory::getDate($row->publish_down);
				$publish_up->setOffset($config->getValue('config.offset'));
				$publish_down->setOffset($config->getValue('config.offset'));
				if ($now->toUnix() <= $publish_up->toUnix() && $row->published == 1) {
					$img = 'publish_y.png';
					$alt = JText::_('Published');
				} else if (($now->toUnix() <= $publish_down->toUnix() || $row->publish_down == $nullDate) && $row->published == 1) {
					$img = 'publish_g.png';
					$alt = JText::_('Published');
				} else if ($now->toUnix() > $publish_down->toUnix() && $row->published == 1) {
					$img = 'publish_r.png';
					$alt = JText::_('Expired');
				} else if ($row->published == 0) {
					$img = 'publish_x.png';
					$alt = JText::_('Unpublished');
				} else if ($row->published == -1) {
					$img = 'disabled.png';
					$alt = JText::_('Archived');
				}

				if ($user->authorize('com_users', 'manage')) {
					if ($row->created_by_alias) {
						$author = $row->created_by_alias;
					} else {
						$author = $row->author;
					}
				} else {
					if ($row->created_by_alias) {
						$author = $row->created_by_alias;
					} else {
						$author = $row->author;
					}
				}

				if ($client->id == 0) {
					if (!$row->access) {
						$color_access = 'style="color: green;"';
					} else if ($row->access == 1) {
						$color_access = 'style="color: red;"';
					} else {
						$color_access = 'style="color: black;"';
					}
				}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $page->getRowOffset($i++); ?>
					</td>
					<td>
						<?php echo '<label class="hasTip" title="' . JText::_('AA_USE_ID_IN_TAG') . '::{article ' . $row->id . '}...{/article}"><a href="javascript://" onclick="articlesanywhere_jInsertEditorText( \'' . $row->id . '\' )">' . $row->id . '</a></label>'; ?>
					</td>
					<td>
						<?php echo '<label class="hasTip" title="' . JText::_('AA_USE_TITLE_IN_TAG') . '::{article ' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '}...{/article}"><a href="javascript://" onclick="articlesanywhere_jInsertEditorText( \'' . addslashes(htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8')) . '\' )">' . htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8') . '</a></label>'; ?>
					</td>
					<td>
						<?php echo '<label class="hasTip" title="' . JText::_('AA_USE_ALIAS_IN_TAG') . '::{article ' . $row->alias . '}...{/article}"><a href="javascript://" onclick="articlesanywhere_jInsertEditorText( \'' . $row->alias . '\' )">' . $row->alias . '</a></label>'; ?>
					</td>
					<td style="text-align:center;">
						<img src="<?php echo JURI::root(true) . '/plugins/system/nnframework/images/' . $img; ?>"
							width="16" height="16" border="0" alt="<?php echo $alt; ?>" title="<?php echo $alt; ?>" />
					</td>
					<td style="text-align:center;">
						<img
							src="<?php echo JURI::root(true) . '/plugins/system/nnframework/images/' . (($row->frontpage) ? 'tick_l.png' : ($row->published != -1 ? 'publish_x_l.png' : 'disabled_l.png')); ?>"
							width="16" height="16" border="0"
							alt="<?php echo ($row->frontpage) ? JText::_('YES') : JText::_('NO'); ?>"
							title="<?php echo ($row->frontpage) ? JText::_('YES') : JText::_('NO'); ?>" />
					</td>
					<td style="text-align:center;">
						<?php echo $row->ordering; ?>
					</td>
					<?php
					if ($client->id == 0) {
						?>
						<td style="text-align:center;">
							<?php
							echo '<span ' . $color_access . '>' . JText::_($row->accesslevel) . '</span>';
							?>
						</td>
						<?php
					}
					?>
					<td>
						<?php echo $row->section; ?>
					</td>
					<td>
						<?php echo $row->category; ?>
					</td>
					<td>
						<?php echo $author; ?>
					</td>
					<td nowrap="nowrap">
						<?php echo JHtml::_('date', $row->created, JText::_('DATE_FORMAT_LC4')); ?>
					</td>
					<td nowrap="nowrap" style="text-align:center;">
						<?php echo $row->hits ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>
	<?php
	}
}