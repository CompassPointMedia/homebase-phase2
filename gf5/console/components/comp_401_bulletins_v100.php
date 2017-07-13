<?php
/* 
todo
	2011-06-20
		* lose headers when just a parent, and add a paragraph at the top
		* plug in to home page (lose current home link/ and there is a link duplicate list_bulletins)
		* setting to not receive bulletin updates
		* view older bulletins link at bottom

bulletinsSuppressByMe
bulletinsSuppressForMe
bulletinPageHub
*/

if(!$bulletinPageHub)$bulletinPageHub='root_bulletins.php';

if(!$refreshComponentOnly){
	?><style type="text/css">
	.yat{
		border-collapse:collapse;
		clear:both;
		}
	.yat th{
		border-bottom:1px solid #000;
		padding:4px 5px 1px 7px;
		}
	.yat td{
		background-image:url("/images/i/grad/v-f9f7e3ff-ffffff00-oso64.png");
		background-repeat:repeat-x;
		/*background-position:-10px -10px;*/
		border-bottom:1px solid #ccc;
		padding:4px 5px 1px 7px;
		}
	.yat h3 a{
		color:darkred;
		}
	</style>
	<script language="javascript" type="text/javascript">
	</script><?php
}
?>

<?php 
if(minroles()<ROLE_CLIENT && !$bulletinsSuppressByMe){ 
	?><h2> Bulletins <u>BY ME</u> </h2>
	<?php 
	if($bulletins=q("SELECT 
		b.*,
		COUNT(u1.un_username) AS 'Read',
		COUNT(u2.un_username) AS 'Dismissed',
		t.ID AS Tree_ID,
		t.Name AS FileName
		FROM gf_bulletins b 
		LEFT JOIN gl_ObjectsTree ot ON b.ID=ot.Objects_ID AND ot.ObjectName='gf_bulletins'
		LEFT JOIN relatebase_tree t ON ot.Tree_ID=t.ID
		LEFT JOIN gf_UniversalBulletins u1 ON b.ID=u1.Bulletins_ID AND u1.Status LIKE 'Read%'  
		LEFT JOIN gf_UniversalBulletins u2 ON b.ID=u2.Bulletins_ID AND u2.Status='Dismissed' 
		WHERE b.bl_unusername='".sun()."'
		GROUP BY b.ID
		ORDER BY b.EffectiveDate DESC", O_ARRAY)){
		?>
		<table class="yat">
		  <thead>
			<tr>
			  <th>&nbsp;</th>
			  <th>Date</th>
			  <th>&nbsp;</th>
			  <th width="220">Title</th>
			  <th>Overview</th>
			  <th class="tac">Clients</th>
			  <th class="tac">Agents</th>
			  <th class="tac">Mgrs.</th>
			  <th class="tac">Admin</th>
			  <th>#Read</th>
			  <th>#Dismissed</th>
			</tr>
		  </thead>
		  <?php
			foreach($bulletins as $v){
				$IncludeGroups=explode(',',$v['IncludeGroups']);
				?><tr id="rm_<?php echo $v['ID']?>">
			<td><a href="bulletins.php?Bulletins_ID=<?php echo $v['ID']?>" title="Click to EDIT this bulletin" onclick="return ow(this.href,'l1_bulletins','700,553');"><img src="/images/i/edit2.gif" align="edit" /></a></td>
			  <td><?php echo date('n/j/Y \a\t g:iA',strtotime($v['EffectiveDate']));?></td>
			  <td><?php
				if($v['Tree_ID']){
					?><a href="resources/bais_01_exe.php?mode=downloadFile&Tree_ID=<?php echo $v['Tree_ID']?>&file=<?php echo urlencode($v['FileName']);?>&suppressPrintEnv=1" target="w2" title="Download documentation"><img src="/images/i/fileicon_general.gif" width="21" height="25" alt="file" /></a><?php
				}else{
					?>&nbsp;<?php
				}
				?></td>
			  <td><h3 class="nullTop nullBottom"><a href="read_bulletins.php?Bulletins_ID=<?php echo $v['ID'];?>" title="Click to REVIEW this bulletin including people who have read or dismissed it" onclick="return ow(this.href,'l1_bulletins','700,700');"><?php echo $v['Title'];?></a></h3>	<em class="gray"><?php echo $v['Description']?></em></td>
			  <td><?php
				$n=explode(' ', strip_tags($v['Contents']));
				$to=45;
				for($i=0;$i<$to;$i++){
					echo $n[$i].' ';
				}
				?></td>
			  <td class="tac"><?php
				echo in_array(ROLE_CLIENT,$IncludeGroups) ? 'Yes' : '&nbsp;';
				?></td>
			  <td class="tac"><?php
				echo in_array(ROLE_AGENT,$IncludeGroups) ? 'Yes' : '&nbsp;';
				?></td>
			  <td class="tac"><?php
				echo in_array(ROLE_MANAGER,$IncludeGroups) ? 'Yes' : '&nbsp;';
				?></td>
			  <td class="tac"><?php
				echo in_array(ROLE_ADMIN,$IncludeGroups) || in_array(ROLE_DBADMIN,$IncludeGroups) ? 'Yes' : '&nbsp;';
				?></td>
			  <td class="tac"><?php echo $v['Read'] ? $v['Read'] : '&nbsp;';?></td>
			  <td class="tac"><?php echo $v['Dismissed'] ? $v['Dismissed'] : '&nbsp;';?></td>
				</tr><?php
			}
			?>
		</table>
		<?php
	}else{
		?>You currently have no bulletins<?php
	}
	?>
	<p>
	  <a href="bulletins.php" title="Write a new bulletin" onclick="return ow(this.href,'l1_bulletins','700,700');">write a new bulletin</a></p><?php 
}
if(!$bulletinsSuppressForMe){
	?>
	<h2>Bulletins <u>FOR ME</u></h2>
	<?php ob_start();?>
	<a style="font-weight:bold;" href="<?php 
	echo $bulletinPageHub.'?'.preg_replace('/&*showRead=[01]/i','',$_SERVER['QUERY_STRING']).($_SERVER['QUERY_STRING'] ? '&':'').'showRead='.($showRead?0:1);
	?>"><?php echo $showRead?'Hide':'Show'?> read or dismissed bulletins</a>
	<?php
	$showReadControl=ob_get_contents();
	ob_end_clean();
	?>
	<?php
	$bulletins=q("SELECT 
	b.*, 
	u.un_firstname, u.un_lastname, u.un_middlename, u.un_email, ub.Status,
	t.ID AS Tree_ID,
	t.Name AS FileName
	FROM gf_bulletins b
	LEFT JOIN gl_ObjectsTree ot ON b.ID=ot.Objects_ID AND ObjectName='gf_bulletins'
	LEFT JOIN relatebase_tree t ON ot.Tree_ID=t.ID
	LEFT JOIN gf_UniversalBulletins ub ON b.ID=ub.Bulletins_ID AND ub.un_username='".sun()."', 
	bais_universal u
	WHERE 
	u.un_username=b.bl_unusername AND 
	b.bl_unusername !='".sun()."' AND
	(0 ".
	($_SESSION['admin']['roles'][ROLE_CLIENT] ? " OR b.IncludeGroups REGEXP('25')" : '').
	($_SESSION['admin']['roles'][ROLE_AGENT] ? " OR b.IncludeGroups REGEXP('10')" : '').
	($_SESSION['admin']['roles'][ROLE_MANAGER] ? " OR b.IncludeGroups REGEXP('3(,|$)')" : '').
	($_SESSION['admin']['roles'][ROLE_ADMIN] ? " OR b.IncludeGroups REGEXP('2(,|$)')" : '').
	($_SESSION['admin']['roles'][ROLE_DBADMIN] ? " OR b.IncludeGroups REGEXP('1(,|$)')" : '')
	.")
	/* any presense in ub means it has been read or dismissed */
	".($showRead ? '' : " AND ub.Bulletins_ID IS NULL")."
	ORDER BY EffectiveDate DESC", O_ARRAY);
	if($bulletins){
		?><div class="fr">
		<?php echo $showReadControl?>  </div>
		<table class="yat">
			<thead>
			<tr>
			<th>Date</th>
			<th>Author</th>
			<th>&nbsp;</th>
			<th>Title</th>
			<th>Overview</th>
			<th>Status</th>
			<th>Actions</th>
			</tr>
			</thead><?php
			foreach($bulletins as $v){
				?><tr id="r_<?php echo $v['ID']?>">
				<td><?php echo date('n/j/Y \a\t g:iA',strtotime($v['EffectiveDate']));?></td>
				<td nowrap="nowrap"><a href="mailto:<?php echo $v['un_email'];?>" title="click to email this person"><?php echo $v['un_lastname'] . ', ' . $v['un_firstname'].($v['un_middlename'] ? ' '.substr($v['un_middlename'],0,1):'');?></a></td>
				<td><?php
				if($v['Tree_ID']){
					?><a href="resources/bais_01_exe.php?mode=downloadFile&Tree_ID=<?php echo $v['Tree_ID']?>&file=<?php echo urlencode($v['FileName']);?>&suppressPrintEnv=1" target="w2" title="Download documentation"><img src="/images/i/fileicon_general.gif" width="21" height="25" alt="file" /></a><?php
				}else{
					?>&nbsp;<?php
				}
				?></td>
				<td><h3 class="nullTop nullBottom"><a href="read_bulletins.php?Bulletins_ID=<?php echo $v['ID'];?>" title="View the full contents of this bulletin" onclick="return ow(this.href,'l1_bulletins','700,700');"><?php echo $v['Title'];?></a></h3>	<em class="gray"><?php echo $v['Description']?></em></td>
				<td><?php
				$n=explode(' ', strip_tags($v['Contents']));
				$to=45;
				for($i=0;$i<$to;$i++){
					echo $n[$i].' ';
				}
				?>		</td>
				<td><?php echo $v['Status'] ? $v['Status'] : 'unread';?></td>
				<td nowrap="nowrap">
				<?php if($v['Status']=='Read' || $v['Status']=='Dismissed') { ?><span style="visibility:hidden;"><?php } ?>
				[<a href="resources/bais_01_exe.php?mode=dismissBulletin&Bulletins_ID=<?php echo $v['ID'];?>" target="w2">Dismiss</a>] 
				<?php if($v['Status']=='Read' || $v['Status']=='Dismissed') { ?></span><?php } ?>
				&nbsp;&nbsp;
				[<a title="View the full contents of this bulletin" href="read_bulletins.php?Bulletins_ID=<?php echo $v['ID'];?>" onclick="return ow(this.href,'l1_bulletins','700,700');">Re<?php if($v['Status']=='Read')echo '-re';?>ad it</a>]		</td>
				</tr><?php
			}
			?>
		</table><?php
	}else{
		?>
	  No current bulletins. <?php echo $showReadControl;?>
	  <?php
	}
}
?>
