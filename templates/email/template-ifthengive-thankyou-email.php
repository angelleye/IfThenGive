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
                    <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: rgb(253, 253, 253); border: 1px solid rgb(220, 220, 220)">
                        <tbody>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="600" style=" color: rgb(255, 255, 255); border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">
                                        <tbody>
                                            <tr>
                                                <td style="padding: 10px; display: block">
                                                    <h1 style="color: rgb(255, 255, 255); font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 rgb(119, 151, 180)">
                                                        <img src="<?php echo ITG_PLUGIN_URL.'/admin/images/ifthengive.png'; ?>" alt="IfThenGive">
                                                    </h1>
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
                                            <div style="width: 100%; margin: 10px auto 25px; float: none; height: auto; color: #f58634; font-weight: 600; text-align: center;">
                                                <strong style="line-height: 25px;padding: 10px 10px 10px 10px;font-weight: 300; letter-spacing: 1px;text-transform: uppercase; margin-bottom:10px; font-size: 15px;">
                                                    <?php echo sprintf('%1$s %2$s<br>%3$s <b>%4$s</b>',
                                                            esc_html__('Hi','ifthengive'),
                                                            $display_name,
                                                            esc_html__(',Thank You for signed up in ','ifthengive'),
                                                            $trigger_name
                                                    );?></strong>
                                                <p style="padding: 10px 10px 10px 10px;font-size: 12px;text-align: center;font-family: inherit; color: #076799">
                                                    <strong>
                                                        <?php
                                                        echo sprintf('%1$s %2$s%3$s %4$s %5$s',
                                                                esc_html__('Each time you will give','ifthengive'),
                                                                $symbol,
                                                                $amount,
                                                                esc_html__('when','ifthengive'),
                                                                $trigger_thing
                                                        );
                                                        ?></strong>
                                                </p>
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