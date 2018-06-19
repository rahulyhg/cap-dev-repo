<?php
require_once realpath(__DIR__ . '/..').'/config.php';
$vocabularies[] = [
    //your custom language variable
    'custom_test' => 'Test',
    //deposit
    'deposit' => 'Deposit',
    'deposit_transaction_create' => 'Create Transaction',
    'deposit_ho' => 'Deposit HO',
    'deposit_id' => 'DepositID',
    'deposit_refid' => 'ReferenceID',
    'deposit_date' => 'Created',
    'deposit_desc' => 'Description',
    'deposit_description' => 'History of all deposit transaction is here.',
    'deposit_credit' => 'Credit',
    'deposit_debit' => 'Debit',
    'deposit_task' => 'Task',
    'deposit_before' => 'Before',
    'deposit_mutation' => 'Mutation',
    'deposit_mutation_data' => 'Data Mutation Balance',
    'deposit_balance' => 'Balance',
    'deposit_topup' => 'Topup',
    'deposit_help_id' => 'DepositID is the username of recipient',
    'deposit_help_refid' => 'ReferenceID is the transaction unique code.',
    'deposit_help_desc' => 'Description about your transaction.',
    'deposit_help_mutation' => 'Input the nominal value of transaction.',
    'deposit_transaction_do' => 'Will submit transaction',
    'deposit_transaction_confirm' => 'This transaction can not be canceled!',
    'deposit_transaction_submit_yes' => 'Yes, Continue Now!',
    //livechat
    'livechat' => 'Live Chat',
    //flexible config
    'flexibleconfig' => 'Flexible Config',
    'flexibleconfig_description' => 'Save unlimited config for flexible use.',
    'flexibleconfig_key' => 'Key',
    'flexibleconfig_value' => 'Value',
    'flexibleconfig_desc' => 'Description',
    'flexibleconfig_delete_warning' => 'Be Careful! Deleting flexible config may broke your app.',
    //global
    'reason' => 'Reason',
    'result' => 'Result',
    'clear' => 'Clear',
    'submit_success' => 'Submit Success!',
    'submit_failed' => 'Submit Failed!',
    //direction
    'next' => 'Next',
    'previous' => 'Previous',
    'go_back' => 'Go Back',
    //wizard
    'step_next' => 'Next <i class="mdi mdi-skip-next"></i>',
    'step_previous' => '<i class="mdi mdi-skip-previous"></i> Previous',
    'step_loading' => 'Loading...',
    'step_pagination' => 'Pagination',
    'step_current' => 'current step:',
    //nominal
    'currency_format' => '$',
    //cargo
    'tariff' => 'Tariff',
    'handling' => 'Handling',
    'district' => 'District',
    'kgp' => 'KGP',
    'kgs' => 'KGS',
    'minkg' => 'Min_Kg',
    'hkgp' => 'H_KGP',
    'hkgs' => 'H_KGS',
    'hminkg' => 'H_Min_Kg',
    'estimation' => 'Estimation',
    'origin' => 'Origin',
    'data_origin' => 'Data Origin',
    'destination' => 'Destination',
    'branch_origin' => 'Branch Origin',
    'branchid_origin' => 'Branch ID Origin',
    'branchid_destination' => 'Branch ID Destination',
    'weight' => 'Weight',
    'actkg' => 'Act. Kg',
    'volkg' => 'Vol. Kg',
    'realkg' => 'Real Kg',
    'days' => 'Day(s)',
    'mode' => 'Mode',
    'modeid' => 'Mode ID',
    'type' => 'Type',
    'customer' => 'Customer',
    'actual' => 'Actual',
    'bag' => 'Bag',
    'waybill' => 'Waybill',
    'pod' => 'Proof of Delivery',
    'void' => 'Void',
    'deliveryid' => 'Delivery ID',
    'return' => 'Return',
    'return_shipper' => 'Shipper Request',
    'return_recipient' => 'Recipient Request',
    'return_origin' => 'Origin Request',
    //dimension
    'length' => 'Length',
    'width' => 'Width',
    'height' => 'Height',
    'volume' => 'Volume',
    'cubication' => 'Cubication',
    //transaction
    'transaction' => 'Transaction',
    'payment_method' => 'Payment Method',
    'shipper' => 'Shipper',
    'consignee' => 'Consignee',
    'payment' => 'Payment',
    'customer_id' => 'Customer ID',
    'customer_type' => 'Customer Type',
    'browse' => 'Browse',
    'shipper_name' => 'Shipper Name',
    'shipper_alias_name' => 'Alias Name',
    'alias' => 'AKA',
    'shipper_address' => 'Shipper Address',
    'shipper_phone' => 'Shipper Phone',
    'shipper_fax' => 'Shipper Fax',
    'reference_id' => 'Referensi ID',
    'attention_name' => 'Attention Name',
    'att' => 'Att.',
    'consignee_name' => 'Consignee Name',
    'consignee_address' => 'Consignee Address',
    'consignee_phone' => 'Consignee Phone',
    'consignee_fax' => 'Consignee Fax',
    //transaction helper
    'transaction_success' => 'Transaction Success!',
    'transaction_failed' => 'Transaction Failed!',
    'transaction_failed_customer' => 'Except Non Member, Customer ID is required to filled!',
    'transaction_input_notice' => 'Fields marked with an asterisk are required.',
    //shipping
    'shipping_cost' => 'Shipping Cost',
    'shipping_cost_admin' => 'Admin Fee',
    'shipping_cost_discount' => 'Discount',
    'shipping_cost_forward' => 'Forwarding Cost',
    'shipping_cost_handling' => 'Handling Cost',
    'shipping_cost_insurance' => 'Insurance Fee',
    'shipping_cost_packing' => 'Packing Cost',
    'shipping_cost_surcharge' => 'Surcharge',
    'shipping_cost_total' => 'Total Cost',
    //insurance
    'insurance' => 'Insurance',
    'insurance_calculate' => 'Calculate Insurance',
    'insurance_rate' => 'Rate Premium',
    //goods
    'goods_value' => 'Nilai Barang',
    'goods_weight' => 'Weight of Goods',
    'goods_detail' => 'Goods Detail',
    'goods_instruction' => 'Instruction',
    'goods_description' => 'Goods Description',
    //help transaction
    'help_browse_customer' => 'Click browse to search Customer ID.',
    'help_reference_id' => 'Reference ID is no partner\'s receipt, so the system can tracking using no partner\'s receipt.',
    'help_dest_id' => 'You have to choose which branch of destination will handle your goods.<br>If <span class="badge badge-inverse">City Courier</span> or <span class="badge badge-inverse">Direct</span> then select your own branch.',
    //help void
    'help_void' => 'Void is a document cancellation',
    'help_void_waybill' => 'Waybill can only be void by Origin',
    //help pod
    'help_pod' => 'POD is a delivery status report',
    'help_pod_waybill' => 'POD Status can only be filled by Destination',
    'help_pod_description' => 'Caution, Please be filled wisely as it will be read by the customer',
    //validation
    'select_destination_required' => 'You have not selected destination branches yet!',
    //input transaction
    'input_browse_customer' => 'Customer ID could be ID Corporate or ID Member...',
    'input_reference_id' => 'Referensi ID must unique...',
    'input_reference_id_val' => 'Input 8-20 chars. Example: '.uniqid(rand(10,99).'/'.rand(1,9).'-'.rand(1,9).'.'),
    //inquiry
    'inquiry' => 'Inquiry',
    'check_tariff' => 'Check Tariff',
    'calculate_tariff' => 'Calculate Tariff',
    //placeholder
    'city_district' => 'City / District',
    //print
    'print' => 'Print',
    'print_preview' => 'Preview Print',
    //print report
    'sheet_for_shipper' => 'Sheet 1 for shipper.',
    'sheet_for_goods' => 'Sheet 2 for POD/Goods.',
    'tel' => 'Tel.',
    'route_shipment' => 'Route of Shipment',
    'date_transaction' => 'Date Transaction',
    'date_send' => 'Date Send',
    'date_received' => 'Date Received',
    'admin' => 'Admin',
    'custid' => 'Cust. ID',
    'refid' => 'Ref. ID',
    'weight_or_volume' => 'Weight/Volume',
    'relation_status' => 'Relation Status',
    //print info
    'print_desktop_notice' => 'If using Mobile, only view that looks broke, but the print remains okay.',
    //signature
    'info_signature_1' => '* Signature',
    'info_signature_2' => '* Signature and Relation Status',
    //report
    'report' => 'Report',
    'firstdate' => 'Firstdate',
    'lastdate' => 'Lastdate',
    'destid' => 'Dest. ID',
    //tracking
    'shipment_history' => 'Shipment History',
    'received_by' => 'Received by'
];