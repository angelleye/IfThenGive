<?php
/**
 * IfThenGive Thankyou Email template.
 *
 * This template can be overriden by copying this file to your-theme/ifthengive/email/template-ifthengive-thankyou-email.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     0.1.0
 */
?>
<div dir="ltr" style="background-color: rgb(245, 245, 245); margin: 0; padding: 70px 0 70px 0; width: 100%; height:100%">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" min-height="100%">
        <tbody>
            <tr>
                <td align="center" valign="top">
                    <table border="0" cellpadding="0" cellspacing="0" style="background-color: rgb(253, 253, 253); border: 1px solid rgb(220, 220, 220)">
                        <tbody>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="600" style=" color: rgb(255, 255, 255); border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">
                                        <tbody>
                                            <tr>
                                                <td style="padding-top: 10px; display: block">
                                                    <div style="text-align: center">
                                                        <img src="<?php echo ITG_PLUGIN_URL . '/admin/images/itg_success.png'; ?>" alt="IfThenGive" style="height: 75px">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <div style="margin-right: -15px; margin-left: -15px;">
                                        <div style="width: 100%;">
                                            <div style="width: 100%; margin: 0 auto 25px; float: none; height: auto; color: #0b79bc; text-align: center; text-transform: uppercase;font-size:16px;line-height: 30px;letter-spacing: 1px;">
                                                <?php
                                                echo sprintf('%1$s, %2$s <br> %3$s <span style="font-weight: 600;">%4$s</span>!', esc_html__('Thank you', 'ifthengive'), $display_name, esc_html__('For giving to', 'ifthengive'), $trigger_name
                                                );
                                                ?>
                                                <div style="color: #0b79bc;">                                                    
                                                <?php
                                                echo sprintf('<span style="color: #f36f21;font-weight: bold;font-size:larger;">%1$s</span> %2$s <span style="color: #f36f21;font-weight: bold;font-size: larger;">%3$s</span> <span style="font-weight: 600;">%4$s%5$s</span>', esc_html__('If', 'ifthengive'), $trigger_thing, esc_html__('Then Give', 'ifthengive'), $symbol, $amount
                                                );
                                                ?>
                                                </div>
                                                <?php
                                                if (is_user_logged_in()) {
                                                ?>
                                                <div>
                                                    <br>
                                                    <a style="    background-color: #26B8F3;
                                                       border-color: #26B8F3;
                                                       color: #fff;
                                                       border-radius: 4px;
                                                       padding: 12px 20px;
                                                       display: inline-block;
                                                       /* padding: 6px 12px; */
                                                       /* margin-bottom: 0; */
                                                       font-size: 14px;
                                                       font-weight: 400;
                                                       line-height: 1.42857143;
                                                       text-align: center;
                                                       white-space: nowrap;
                                                       /* vertical-align: middle; */
                                                       -ms-touch-action: manipulation;
                                                       touch-action: manipulation;
                                                       cursor: pointer;
                                                       /* -webkit-user-select: none; */
                                                       -moz-user-select: none;
                                                       -ms-user-select: none;
                                                       user-select: none;
                                                       border: 1px solid transparent;
                                                       text-decoration: none;" href="<?php echo site_url('itg-account'); ?>">
<?php echo sprintf('%1$s', esc_html__('MANAGE YOUR ACCOUNT', 'ifthengive')); ?>
                                                    </a>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>