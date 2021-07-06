<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $email['title'];?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
</head>
<body>

<table width="500" cellpadding="0" cellspacing="0" border="0" align="center">

	<tr bgcolor="#ffffff">
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr><td colspan="4" height="20"></td></tr>
				<tr>
					<td width="20"></td>
					<td><img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/logo-light.png" alt="Swift performance" width="104" height="60" border="0" /></td>
					<td width="20"></td>
				</tr>
				<tr><td colspan="4" height="20"></td></tr>
			</table>
		</td>
	</tr>


	<tr style="background: rgb(242,125,16);background: linear-gradient(328deg, rgba(242,125,16,1) 0%, rgba(237,47,29,1) 100%);">
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr><td colspan="3" height="30"></td></tr>
				<tr>
					<td width="30"></td>
					<td style="font-family:'roboto',tahoma,arial,sans-serif; color:#fff; font-size:30px; line-height:38px; font-weight:bold" align="center">
						<?php echo sprintf(esc_html__('Hello %s!', 'swift-performance'), $email['name']);?>
					</td>
					<td width="30"></td>
				</tr>
				<tr><td colspan="3" height="30"></td></tr>
			</table>
		</td>
	</tr>



	<tr bgcolor="#fff">
		<td style="padding: 30px 0">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<tr>
						<td width="30"></td>

						<td style="font-family:'roboto',tahoma,arial,sans-serif; color:#ed2f1d; text-align: center; line-height: 28px; font-size: 20px; font-weight: bold;">
							<?php echo $email['title'];?>
						</td>

						<td colspan="3" height="30"></td>
					</tr>
				<tr><td colspan="3" height="30"></td></tr>
                        <tr>
					<td width="30"></td>
					<td style="font-family:'roboto',tahoma,arial,sans-serif; color:#595959; padding:0; margin:0 0 5px 0; font-weight:normal; font-size:18px;">
						<?php echo $email['content'];?>
					</td>
					<td width="30"></td>
				</tr>
                        <tr>
					<td width="30"></td>
					<td style="text-align:center">
                                    <div style="margin:40px 0">
                                          <?php echo $email['score_from'];?>
                                          <img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/arrow-black.png" style="vertical-align:middle;">
                                          <?php echo $email['score_to'];?>
                                    </div>
                                    <a target="_blank" href="<?php echo Swift_Performance::get_upgrade_url('email-notification-' . $credit . '-' . $notification);?>" style="background-color: #2cc58d;border: none;border-radius: 50px;color: #1b1d1f;display: inline-block;font-size: 1;font-weight: bold;letter-spacing: 1px;line-height: 1.429em;padding: 9px 23px;text-decoration: none;color: #fff;text-transform: uppercase;font-family: Roboto;"><?php esc_html_e('Upgrade to unlimited', 'swift-performance');?></a>
                                    <br><br>
                                    <small style="display:block;width:100%;text-align:right;"><sup>*</sup><?php echo sprintf(esc_html__('your monthly qouta will be reset in %d days'), round((strtotime('first day of next month')-time())/86400))?></small>

					</td>
					<td width="30"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr bgcolor="#f7f7f7">
		<td>
			<table width="400" cellpadding="0" cellspacing="0" border="0" align="center">
				<tr><td colspan="3" height="30"></td></tr>
				<tr>
					<td valign="top"><img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/icon1.png" alt="icon" width="59" height="59" border="0"/></td>
					<td width="30"></td>
					<td>
						<p style="font-family:'roboto',tahoma,arial,sans-serif; color:#595959; padding:0; margin:0 0 5px 0; font-weight:bold; font-size:18px;"><?php esc_html_e('Do you have questions?', 'swift-performance');?></p>
						<p style="font-family:'roboto',tahoma,arial,sans-serif; color:#595959; padding:0; margin:0 0 5px 0; font-size: 14px"><?php esc_html_e('Check Swift Performance documentation for quick answers', 'swift-performance');?></p>
						<p align="right" style="margin:0;padding:0"><a href="https://docs.swiftperformance.io" target="_blank" style="font-family:'roboto',tahoma,arial,sans-serif; color:#41be6a"><?php esc_html_e('Swift Performance documentation', 'swift-performance');?></a></p>
					</td>
				</tr>
				<tr><td colspan="3" height="20"></td></tr>
				<tr>
					<td valign="top"><img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/icon2.png" alt="icon" width="59" height="59" border="0"/></td>
					<td width="30"></td>
					<td>
						<p style="font-family:'roboto',tahoma,arial,sans-serif; color:#595959; padding:0; margin:0 0 5px 0; font-weight:bold; font-size:18px;"><?php esc_html_e('Facebook community', 'swift-performance');?></p>
						<p style="font-family:'roboto',tahoma,arial,sans-serif; color:#595959; padding:0; margin:0 0 5px 0; font-size: 14px"><?php esc_html_e('Swift Performance Facebook community members are always happy to help', 'swift-performance');?></p>
						<p align="right" style="margin:0;padding:0"><a href="https://facebook.com/groups/SwiftPerformanceUsers" target="_blank" style="font-family:'roboto',tahoma,arial,sans-serif; color:#41be6a"><?php esc_html_e('Join to Swift Performance Users', 'swift-performance');?></a></p>
					</td>
				</tr>
				<tr><td colspan="3" height="40"></td></tr>
			</table>
		</td>
	</tr>

	<tr bgcolor="#f7f7f7"><td height="25"></td></tr>

	<tr bgcolor="#343543">
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr><td colspan="4" height="20"></td></tr>
				<tr>
					<td width="20"></td>
					<td>
						<img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/footer-logo.png" alt="Swift performance" width="99" height="57" border="0"/>
					</td>
					<td align="right">
						<p style="font-family:'roboto',tahoma,arial,sans-serif; font-size: 25px; color:#505163; font-weight:bold; padding: 0; margin: 0 0 6px 0"><?php esc_html_e('Speed Up WordPress', 'swift-performance');?></p>
						<p style="font-family:'roboto',tahoma,arial,sans-serif; font-size: 16px; color:#616375; padding:0; margin: 0"><?php esc_html_e('is not rocket science anymore', 'swift-performance');?></p>
					</td>
					<td width="20"></td>
				</tr>
				<tr><td colspan="4" height="20"></td></tr>
			</table>
		</td>
	</tr>


	<tr bgcolor="#1b1c23">
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr><td colspan="4" height="10"></td></tr>
				<tr>
					<td width="20"></td>
					<td style="font-family:'roboto',tahoma,arial,sans-serif; color:#6d6e84; font-size:10px">
						<a style="text-decoration:none;color:#6d6e84" href="https://swiftperformance.io">swiftperformance.io</a>
					</td>
					<td align="right">
						<a href="https://facebook.com/groups/SwiftPerformanceUsers" target="_blank"><img src="https://swiftperformance.io/wp-content/themes/swiftperformance/images/email/facebook2.png" border="0" alt="Facebook" width="15" height="15"/></a>
					</td>
					<td width="20"></td>
				</tr>
				<tr><td colspan="4" height="10"></td></tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>