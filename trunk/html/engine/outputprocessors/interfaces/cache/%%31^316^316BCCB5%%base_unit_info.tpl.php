<?php /* Smarty version 2.6.16, created on 2008-03-25 14:40:25
         compiled from base_unit_info.tpl */ ?>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td width="80" align="center" valign="top"><img src="images/<?php echo $this->_tpl_vars['info']['icon']; ?>
" width="70" height="85"></td>
    <td valign="top">
		<div style="font-family:Georgia, 'Times New Roman', Times, serif; font-size: 14px;color: #000066;font-weight: bold;"><?php echo $this->_tpl_vars['info']['title']; ?>
</div>
		<div style="font-family:Georgia, 'Times New Roman', Times, serif; font-size: 12px;"><i><?php echo $this->_tpl_vars['info']['desc']; ?>
</i><br><br><?php echo $this->_tpl_vars['info']['details']; ?>
</div></td>
  </tr>
</table>
<center><a href="javascript:dispose_win('wininfo');"><img src="images/UI/ok.gif" border="0"></a></center>