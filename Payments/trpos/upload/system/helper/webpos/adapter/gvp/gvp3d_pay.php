<?php
/**
 * @package        Arastta eCommerce
 * @copyright      Copyright (C) 2015 Arastta Association. All rights reserved. (arastta.org)
 * @credits        See CREDITS.txt for credits and other copyright notices.
 * @license        GNU General Public License version 3; see LICENSE.txt
 */
  
class gvp3dPay
{
    private function createHash($terminal_id, $oid, $amount, $okUrl, $failUrl, $type, $instalment, $storekey, $provaut_password)
    {
        $secData = strtoupper(sha1($provaut_password . "0" . $terminal_id));
        $hashstr = $terminal_id . $oid . $amount . $okUrl . $failUrl . $type . $instalment . $storekey . $secData;
        $hash    = strtoupper(sha1($hashstr));

        return $hash;
    }

    private function createForm($bank)
    {
        if ($bank['instalment'] != 0) {
            $instalment = $bank['instalment'];
        } else {
            $instalment = "";
        }

        $amount = (int) ($bank['total'] * 100);
        $hash   = $this->createHash($bank['gvp_terminal_id'], $bank['order_id'], $amount, $bank['success_url'], $bank['fail_url'], "sales", $instalment, $bank['gvp_3D_storekey'], $bank['gvp_provaut_password']);

        $inputs = array();
        $inputs = array('secure3dsecuritylevel' => "3D_PAY", //3D_PAY,3D_FULL,3D_HALF @TODO: should create a variable for this
                        'cardnumber'            => $bank['cc_number'],
                        'cardexpiredatemonth'   => $bank['cc_expire_date_month'],
                        'cardexpiredateyear'    => $bank['cc_expire_date_year'],
                        'cardcvv2'              => $bank['cc_cvv2'],
                        'mode'                  => "PROD",
                        'apiversion'            => "v0.01",
                        'terminalprovuserid'    => "PROVAUT",
                        'terminaluserid'        => $bank['gvp_user_name'],
                        'terminalmerchantid'    => $bank['gvp_merchant_id'],
                        'txntype'               => "sales",
                        'txnamount'             => $amount,
                        'txncurrencycode'       => "949",
                        'txninstallmentcount'   => $instalment,
                        'orderid'               => $bank['order_id'],
                        'terminalid'            => $bank['gvp_terminal_id'],
                        'successurl'            => $bank['success_url'],
                        'errorurl'              => $bank['fail_url'],
                        'customeripaddress'     => $bank['customer_ip'],
                        'customeremailaddress'  => "",
                        'secure3dhash'          => $hash,
                        'bank_id'               => $bank['bank_id'],
                        'oid'                   => $bank['order_id']
        );

        $action = '';

        if ($bank['mode'] == 'live') {
            $action = $bank['gvp_3D_url'];
        } else {
            if ($bank['mode'] == 'test') {
                $action = $bank['gvp_test_url'];
            }
        }

        $form = '<form id="trpos_form" name="trpos_form" method="post" action="' . $action . '">';

        foreach ($inputs as $key => $value) {
            $form .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        }

        $form .= '</form>';

        return $form;
    }

    public function methodResponse($bank)
    {
        $response         = array();
        $response['form'] = $this->createForm($bank);
        //$response['redirect']=;
        //$response['error']=;
        return $response;
    }

    public function bankResponse($bank_response, $bank)
    {
        $response            = array();
        $response['message'] = '';

        $mdStatus = $bank_response['mdstatus'];// if mdstatus 1,2,3,4 then 3D authentication is successful, if mdstatus 5,6,7,8,9,0 then 3D authentication is FAILED

        if ($bank_response['secure3dsecuritylevel'] === "3D_FULL") {
            $mdArray = array('1');
        } else {
            $mdArray = array('1', '2', '3', '4');
        }

        if (in_array($mdStatus, $mdArray)) {
            $response['message'] .= '3D Onayı Başarılı.<br/>';
            $ProcReturnCode = $bank_response['procreturncode'];
            $Response       = $bank_response['response'];

            if ($ProcReturnCode == "00" || $Response === "Approved") {
                $response['result'] = 1;
                $response['message'] .= 'Ödeme Başarılı<br/>';
                $response['message'] .= 'AuthCode : ' . $bank_response['authcode'] . '<br/>';
                $response['message'] .= 'Response : ' . $Response . '<br/>';
            } else {
                $response['result'] = 0;
                $response['message'] .= 'Ödeme Başarısız.<br/>';
                $response['message'] .= 'Response : ' . $Response . '<br/>';
                $response['message'] .= 'ErrMsg : ' . $bank_response['errmsg'] . '<br/>';
            }

        } else {
            $response['result'] = 0;
            $response['message'] .= '3D doğrulama başarısız<br/>';
            $response['message'] .= $bank_response['mderrormessage'];

        }
        //print_r($response);
        return $response;
    }
}