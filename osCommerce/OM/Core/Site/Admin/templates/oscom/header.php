<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
?>

<div id="adminMenu">
  <ul class="apps">
    <li class="shortcuts"><?php 
echo HTML::link(OSCOM::get_link(null, OSCOM::get_default_site_application()), HTML::image(OSCOM::get_public_site_link('images/oscommerce_icon.png'), null, 16, 16));
?></li>

<?php 
if (isset($_SESSION[OSCOM::get_site()]['id'])) {
    echo '  <li><a href="#">Applications &#9662;</a>' . '    <ul>';
    foreach (Access::get_levels() as $group => $links) {
        $application = current($links);
        echo '      <li><a href="' . OSCOM::get_link(null, $application['module']) . '"><span style="float: right;">&#9656;</span>' . Access::get_group_title($group) . '</a>' . '        <ul>';
        foreach ($links as $link) {
            echo '          <li><a href="' . OSCOM::get_link(null, $link['module']) . '">' . $OSCOM_Template->get_icon(16, $link['icon']) . '&nbsp;' . $link['title'] . '</a></li>';
        }
        echo '        </ul>' . '      </li>';
    }
    echo '    </ul>' . '  </li>';
}
echo '  <li><a href="' . OSCOM::get_link('Shop', 'Index', null, 'NONSSL', false) . '" target="_blank">' . OSCOM::get_def('header_title_online_catalog') . '</a></li>' . '  <li><a href="http://www.oscommerce.com" target="_blank">' . OSCOM::get_def('header_title_help') . ' &#9662;</a>' . '    <ul>' . '      <li><a href="http://www.oscommerce.com" target="_blank">osCommerce Support Site</a></li>' . '      <li><a href="http://www.oscommerce.info" target="_blank">Online Documentation</a></li>' . '      <li><a href="http://forums.oscommerce.com" target="_blank">Community Support Forums</a></li>' . '      <li><a href="http://addons.oscommerce.com" target="_blank">Add-Ons Site</a></li>' . '      <li><a href="http://forums.oscommerce.com/tracker/project-4-oscommerce-online-merchant-v3x/" target="_blank">Bug Reporter</a></li>' . '    </ul>' . '  </li>';
?>

  </ul>

<?php 
$total_shortcuts = 0;
if (isset($_SESSION[OSCOM::get_site()]['id'])) {
    echo '<ul class="apps" style="float: right;">';
    if ($OSCOM_Application->can_link_to()) {
        if (Access::is_shortcut(OSCOM::get_site_application())) {
            echo '  <li class="shortcuts">' . HTML::link(OSCOM::get_link(null, 'Dashboard', 'RemoveShortcut&shortcut=' . OSCOM::get_site_application()), HTML::icon('shortcut_remove.png')) . '</li>';
        } else {
            echo '  <li class="shortcuts">' . HTML::link(OSCOM::get_link(null, 'Dashboard', 'AddShortcut&shortcut=' . OSCOM::get_site_application()), HTML::icon('shortcut_add.png')) . '</li>';
        }
    }
    if (Access::has_shortcut()) {
        echo '  <li class="shortcuts">';
        foreach (Access::get_shortcuts() as $shortcut) {
            echo '<a href="' . OSCOM::get_link(null, $shortcut['module']) . '" id="shortcut-' . $shortcut['module'] . '">' . $OSCOM_Template->get_icon(16, $shortcut['icon'], $shortcut['title']) . '<div class="notBubble"></div></a>';
            $total_shortcuts++;
        }
        echo '  </li>';
    }
    echo '  <li><a href="#">' . HTML::output_protected($_SESSION[OSCOM::get_site()]['username']) . ' &#9662;</a>' . '    <ul>' . '      <li><a href="' . OSCOM::get_link(null, 'Login', 'Logoff') . '">' . OSCOM::get_def('header_title_logoff') . '</a></li>' . '    </ul>' . '  </li>' . '</ul>';
}
?>

</div>

<script type="text/javascript">
  $('#adminMenu .apps').droppy({speed: 0});
  $('#adminMenu .apps li img').tipsy();
</script>

<?php 
if (isset($_SESSION[OSCOM::get_site()]['id'])) {
    ?>

<script type="text/javascript">
  var totalShortcuts = <?php 
    echo $total_shortcuts;
    ?>;
  var wkn = new Object;

  if ( $.cookie('wkn') ) {
    wkn = $.secureEvalJSON($.cookie('wkn'));
  }

  function updateShortcutNotifications(resetApplication) {
    $.getJSON('<?php 
    echo OSCOM::get_rpc_link('Admin', 'Dashboard', 'GetShortcutNotifications&reset=RESETAPP');
    ?>'.replace('RESETAPP', resetApplication), function (data) {
      $.each(data, function(key, val) {
        if ( $('#shortcut-' + key + ' .notBubble').html != val ) {
          if ( val > 0 || val.length > 0 ) {
            $('#shortcut-' + key + ' .notBubble').html(val).show();

            if ( (typeof webkitNotifications != 'undefined') && (webkitNotifications.checkPermission() == 0) ) {
              if ( typeof wkn[key] == 'undefined' ) {
                wkn[key] = new Object;
              }

              if ( wkn[key].value != val ) {
                wkn[key].value = val;
                wkn[key].n = webkitNotifications.createNotification('<?php 
    echo OSCOM::get_public_site_link('images/applications/32/APPICON.png');
    ?>'.replace('APPICON', key), key, val);
                wkn[key].n.replaceId = key;
                wkn[key].n.ondisplay = function(event) {
                  setTimeout(function() {
                    event.currentTarget.cancel();
                  }, 5000);
                };
                wkn[key].n.show();
              }
            }
          } else {
            $('#shortcut-' + key + ' .notBubble').hide();
          }
        }
      });

      $.cookie('wkn', $.toJSON(wkn));
    });
  }

  $(document).ready(function() {
    if ( totalShortcuts > 0 ) {
      updateShortcutNotifications(typeof resetShortcutNotification != 'undefined' ? '<?php 
    echo OSCOM::get_site_application();
    ?>' : null);

      setInterval('updateShortcutNotifications()', 10000);
    }
  });

  if ( (typeof window.external.msAddSiteMode != 'undefined') && window.external.msIsSiteMode() ) {

<?php 
    if (Access::has_shortcut()) {
        echo '    window.external.msSiteModeClearJumplist();' . "\n" . '    window.external.msSiteModeCreateJumplist("Shortcuts");' . "\n";
        foreach (Access::get_shortcuts() as $shortcut) {
            echo '    window.external.msSiteModeAddJumpListItem("' . $shortcut['title'] . '", "' . OSCOM::get_link(null, $shortcut['module']) . '", "", "self");' . "\n";
        }
        echo '    window.external.msSiteModeShowJumplist();' . "\n";
    }
    ?>

  }
</script>

<?php 
}