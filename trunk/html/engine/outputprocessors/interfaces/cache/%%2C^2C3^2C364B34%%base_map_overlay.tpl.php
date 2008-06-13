<?php /* Smarty version 2.6.16, created on 2007-09-14 21:02:05
         compiled from base_map_overlay.tpl */ ?>
<table border="0" cellspacing="0" cellpadding="0">
<?php $_from = $this->_tpl_vars['map']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['grid_y'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['grid_y']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['grid_y']):
        $this->_foreach['grid_y']['iteration']++;
?>
	<tr>
	<?php $_from = $this->_tpl_vars['grid_y']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['grid_x'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['grid_x']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['cell']):
        $this->_foreach['grid_x']['iteration']++;
?>
		<td class="map" style="<?php echo $this->_tpl_vars['cell']['style']; ?>
" valign="middle" align="center"><?php echo $this->_tpl_vars['cell']['html']; ?>
</td>
	<?php endforeach; endif; unset($_from); ?>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>