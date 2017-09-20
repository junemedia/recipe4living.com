
<div id="header">
  <div class="outer" style="padding-top:15px">
  <h1>
    <a href="<?= SITEURL ?>">Recipe4living Admin</a></h1>
    <div class="links">
      <ul>
        <li><? print(Date("l F jS, Y")); ?></li>
        <li>|</li>
        <li>Logged in as: <strong><?= Template::get('adminName'); ?></strong></li>
        <li>|</li>
        <li><a href="/account/logout/">Logout</a></li>
      </ul>
    </div>
  </div>
</div>

<div id="sub_nav">
  <div class="outer">
    <ul>
    <?php
      foreach ($nav as $k => $n) {
        $on = false;
        foreach ($n['on'] as $o) {
          if ($o == $menu_slug) { $on = true; break; }
        }
        $nav_num = count($n['children']);
    ?>
      <li <? if ($nav_num) { ?>onmouseover="navOn('<?=$k?>',1);" onmouseout="navOff('<?=$k?>');"<? } ?>>
        <? if ($nav_num) { ?>
        <div class="nav_inner" style="display:none" id="navpop_<?=$k?>">
          <div id="dd_products">
            <?
            foreach ($n['children'] as $clink => $ctext) {
              ?>
              <div><a href="<?=$clink?>"><?=$ctext?></a></div>
              <?
            }
            ?>
          </div>
        </div>
        <?  } ?>
        <div class="nav_outer <?=$on?'on':''?>">
          <a href="<?=$n['link']?>" id="navbut_<?=$k?>"><?=$n['name']?></a>
        </div>
      </li>
    <?
      }
    ?>
    </ul>
  </div>
</div>
