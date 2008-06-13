<?php /* Smarty version 2.6.16, created on 2008-05-12 01:34:42
         compiled from base_interface.container.tpl */ ?>
<div class="bag_container">
<?php echo $this->_tpl_vars['_my']['header']; ?>

<?php if (is_array ( $this->_tpl_vars['_my']['objects'] ) && count ( $this->_tpl_vars['_my']['objects'] ) > 0): ?>
<ul>
<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['_my']['objects']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['id']['show'] = true;
$this->_sections['id']['max'] = $this->_sections['id']['loop'];
$this->_sections['id']['step'] = 1;
$this->_sections['id']['start'] = $this->_sections['id']['step'] > 0 ? 0 : $this->_sections['id']['loop']-1;
if ($this->_sections['id']['show']) {
    $this->_sections['id']['total'] = $this->_sections['id']['loop'];
    if ($this->_sections['id']['total'] == 0)
        $this->_sections['id']['show'] = false;
} else
    $this->_sections['id']['total'] = 0;
if ($this->_sections['id']['show']):

            for ($this->_sections['id']['index'] = $this->_sections['id']['start'], $this->_sections['id']['iteration'] = 1;
                 $this->_sections['id']['iteration'] <= $this->_sections['id']['total'];
                 $this->_sections['id']['index'] += $this->_sections['id']['step'], $this->_sections['id']['iteration']++):
$this->_sections['id']['rownum'] = $this->_sections['id']['iteration'];
$this->_sections['id']['index_prev'] = $this->_sections['id']['index'] - $this->_sections['id']['step'];
$this->_sections['id']['index_next'] = $this->_sections['id']['index'] + $this->_sections['id']['step'];
$this->_sections['id']['first']      = ($this->_sections['id']['iteration'] == 1);
$this->_sections['id']['last']       = ($this->_sections['id']['iteration'] == $this->_sections['id']['total']);
?>
<li>
	<img src="images/inventory/<?php echo $this->_tpl_vars['_my']['objects'][$this->_sections['id']['index']]['image']; ?>
" />
	<a href="javascript:gloryIO('?a=guidinfo&guid=<?php echo $this->_tpl_vars['_my']['objects'][$this->_sections['id']['index']]['guid']; ?>
');"><?php echo $this->_tpl_vars['_my']['objects'][$this->_sections['id']['index']]['name']; ?>
</a>
	<span><?php echo $this->_tpl_vars['_my']['objects'][$this->_sections['id']['index']]['desc']; ?>
</span>
	<p><?php echo $this->_tpl_vars['_my']['objects'][$this->_sections['id']['index']]['cost']; ?>
</p>
</li>
<?php endfor; endif; ?>
</ul>
<?php else: ?>
<img class="web" src="images/UI/web.gif" />
<p class="empty" align="center">There are no items here</p>
<?php endif; ?>
</div>