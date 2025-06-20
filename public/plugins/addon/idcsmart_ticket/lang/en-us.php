<?php

return [
    'success_message' => 'Request successful',
    'error_message' => 'Request failure',
    'delete_success' => 'Successfully deleted',
    'delete_fail' => 'Delete failed',
    'update_success' => 'Successfully edited',
    'cannot_repeat_opreate' => 'Non repeatable operation',
    'ticket_status_is_not_exist' => 'The work order status does not exist',
    'ticket_urge_success' => 'Reminder successful',
    'ticket_close_success' => 'Successfully closed the work order',
    'param_error' => 'parameter error',
    'ticket_title_require' => 'Please enter the work order title',
    'ticket_title_max' => 'The maximum number of characters in the work order title is 150',
    'ticket_type_id_require' => 'Work order type is required',
    'ticket_type_id_error' => 'Work order type error',
    'ticket_content_max' => 'Reply content should not exceed 3000 characters',
    'ticket_host_is_not_exist' => 'The selected product does not exist',
    'ticket_host_select' => 'Please select related products',
    'ticket_host_due_can_not_reply' => 'The service has expired and cannot be replied. Please purchase the service',
    'ticket_is_not_exist' => 'The work order does not exist',
    'ticket_reply_is_not_exist' => 'The work order reply does not exist',
    'ticket_status_is_not_allowed_urge' => 'The work order status is resolved or closed, and cannot be reminder',
    'ticket_is_closed' => 'Cannot be closed repeatedly',
    'admin_role_is_not_exist' => 'Department does not exist',
    'ticket_type_name_require' => 'Please enter the name of the work order type',
    'ticket_type_name_require_max' => 'The name of the work order type should not exceed 150 characters',
    'ticket_type_admin_role_id_require' => 'Department ID is required',
    'ticket_type_is_not_exist' => 'The work order type does not exist',
    'ticket_type_already_exist' => 'The work order department already exists',
    'ticket_type_admin_require' => 'Please select administrator',
    'ticket_type_admin_not_found' => 'The administrator does not exist',
    'ticket_is_pending_can_handling' => 'The work order can only be operated in the waiting to receive status',
    'ticket_handle_success' => 'Successfully received the work order',
    'ticket_is_pending_cannot_resolved' => 'The pending status is not operational',
    'ticket_resolved_success' => 'The work order has been resolved',
    'admin_is_not_exist' => 'The specified personnel in the selected department do not exist',
    'client_is_not_exist' => 'The user does not exist',
    'ticket_admin_role_id_require' => 'Please select a department',
    'ticket_priority_require' => 'Please select the level of urgency',
    'ticket_priority_in' => 'The emergency level is medium or high',
    'ticket_is_pending_cannot_forward' => 'Pending status cannot be forwarded',
    'ticket_forward_success' => 'Internal work order forwarding succeeded',
    'ticket_current_admin_cannot_operate' => 'The current administrator does not have permission to operate',
    'ticket_current_admin_cannot_reply' => 'The current administrator cannot reply',
    'ticket_attachment_name_require' => 'Attachment name is required',
    'ticket_attachment_is_not_exist' => 'The attachment does not exist',
    'ticket_admin_is_not_exist' => 'The person does not exist',
    'ticket_urge_time_limit_15_m' => 'We have received your reminder notification and will process your work order as soon as possible. Thank you for your support and cooperation',

    # 日志
    'ticket_log_client_create_ticket' => '{client} Create a new work order: {ticket_id}',
    'ticket_log_admin_create_ticket' => 'Administrator {admin} creates a new work order: {ticket_id}',
    'ticket_log_client_reply_ticket' => '{client} Reply to work order: {ticket_id} ',
    'ticket_log_client_urge_ticket' => '{client} Reminder: {ticket_id}',
    'ticket_log_client_close_ticket' => '{client} Close work order: {ticket_id}',

    'ticket_log_admin_reply_ticket' => '{admin} Reply to the work order: {ticket_id}',
    'ticket_log_admin_receive_ticket' => '{admin} Accept work order: {ticket_id}',
    'ticket_log_admin_resolved_ticket' => '{admin} marked work order: {ticket_id} resolved',
    'ticket_log_create_ticket_internal' => '{admin} Add internal work order: {ticket_id}',
    'ticket_log_admin_create_ticket_notes' => '{admin} Create work order {ticket_id} Notes: {content}',
    'ticket_log_admin_update_ticket_reply' => '{admin} Edit the message replied by {name} ',
    'ticket_log_admin_delete_ticket_reply' => '{admin} Delete the message replied by {name} ',
    'ticket_log_admin_update_ticket_status' => '{admin} Change the work order {ticket} to {status}',
    'ticket_log_admin_update_ticket_type' => '{admin} Change the work order {ticket} to {type}',
    'ticket_log_admin_reply_ticket_admin' => 'The tracking person for work order {ticket_id} has been changed to {admin}',
    'ticket_log_admin_update_ticket_content' => 'Modify the content of the work order {ticket_id} to {content}',
    'ticket_log_admin_ticket_forwad' => 'Work order {ticket_id} follow-up department changed to {admin_role}',

    # 导航
    'nav_plugin_addon_idcsmart_ticket' => 'Work order',
    'nav_plugin_addon_ticket' => 'Work order',
    'nav_plugin_addon_ticket_list' => 'User work orders',
    'nav_plugin_addon_ticket_internal_list' => 'Internal work orders',

    # 权限
    'auth_user_detail_ticket' => 'Ticket',
    'auth_user_detail_ticket_view' => 'View page',
    'auth_user_detail_ticket_transfer_ticket' => 'Transfer ticket',
    'auth_user_detail_ticket_close_ticket' => 'Close ticket',
    'auth_user_detail_ticket_detail' => 'View ticket details',
    'auth_user_ticket' => 'User ticket',
    'auth_user_ticket_list' => 'Ticket list',
    'auth_user_ticket_list_view' => 'View page',
    'auth_user_ticket_list_create_ticket' => 'New ticket',
    'auth_user_ticket_list_transfer_ticket' => 'Transfer ticket',
    'auth_user_ticket_list_close_ticket' => 'Close ticket',
    'auth_user_ticket_list_ticket_detail' => 'View ticket details',
    'auth_user_ticket_configuration' => 'Ticket configuration',
    'auth_user_ticket_configuration_view' => 'View page',
    'auth_user_ticket_configuration_ticket_department' => 'Work Order Department',
    'auth_user_ticket_configuration_ticket_status' => 'Ticket status',
    'auth_user_ticket_configuration_save_ticket_notice' => 'Save ticket notification',
    'auth_user_ticket_configuration_prereply' => 'Preset reply',
    'auth_user_ticket_detail' => 'Ticket details',
    'auth_user_ticket_detail_view' => 'View page',
    'auth_user_ticket_detail_reply_ticket' => 'Reply to the ticket',
    'auth_user_ticket_detail_create_notes' => 'Add notes',
    'auth_user_ticket_detail_use_prereply' => 'Use prereply',
    'auth_user_ticket_detail_ticket_log' => 'Ticket log record',
    'auth_user_ticket_detail_save_ticket' => 'Save ticket information',

    # 权限规则
    'auth_rule_plugin_addon_ticket_list' => 'User work orders',
    'auth_rule_plugin_addon_ticket_receive' => 'Accept the work order',
    'auth_rule_plugin_addon_ticket_resolved' => 'Resolve work orders',
    'auth_rule_plugin_addon_ticket_index' => 'Work Order Details',
    'auth_rule_plugin_addon_ticket_reply' => 'Reply to the work order',
    'auth_rule_plugin_addon_ticket_download' => 'Download attachments',
    'auth_rule_plugin_addon_ticket_internal_list' => 'Internal work orders',
    'auth_rule_plugin_addon_ticket_internal_index' => 'Internal work order details',
    'auth_rule_plugin_addon_ticket_internal_create' => 'Create internal work order',
    'auth_rule_plugin_addon_ticket_internal_receive' => 'Accept internal work orders',
    'auth_rule_plugin_addon_ticket_internal_resolved' => 'Resolve internal work orders',
    'auth_rule_plugin_addon_ticket_internal_reply' => 'Reply to internal work orders',
    'auth_rule_plugin_addon_ticket_internal_forward' => 'Forward internal work orders',

    # 会员中心权限
    'clientarea_auth_plugin_addon_ticket' => 'Work Order Center',
    'clientarea_auth_plugin_addon_ticket_view' => 'View',
    'clientarea_auth_plugin_addon_ticket_manager' => 'Manage',

    # 会员中心权限规则
    'clientarea_auth_rule_plugin_addon_ticket_list' => 'Work order list',
    'clientarea_auth_rule_plugin_addon_ticket_statistic' => 'Work order statistics',
    'clientarea_auth_rule_plugin_addon_ticket_index' => 'View work order',
    'clientarea_auth_rule_plugin_addon_ticket_create' => 'Create a work order',
    'clientarea_auth_rule_plugin_addon_ticket_reply' => 'Reply to the work order',
    'clientarea_auth_rule_plugin_addon_ticket_urge' => 'Reminder',
    'clientarea_auth_rule_plugin_addon_ticket_close' => 'Close the work order',
    'clientarea_auth_rule_plugin_addon_ticket_download' => 'Work order attachment download',

    # 工单状态
    'ticket_status_name_require' => 'Please enter the work order status',
    'ticket_status_name_max' => 'The work order status does not exceed 255 characters',
    'ticket_status_color_require' => 'Please enter the color value',
    'ticket_status_color_max' => 'The color value should not exceed 255 characters',
    'ticket_status_status_require' => 'Please select the completion status',
    'ticket_status_status_in' => 'The completion status value is 1 or 0',
    'ticket_ticket_status_is_not_exist' => 'The work order status does not exist',
    'ticket_ticket_status_cannot_update' => 'The default status cannot be updated',
    'ticket_ticket_status_cannot_delete' => 'The default state cannot be deleted',
    'ticket_ticket_prereply_is_not_exist' => 'The default reply to the work order does not exist',

    'client_create_ticket_send_mail' => 'Customer new work order, send mail',
    'client_create_ticket_send_sms' => 'Customers add work orders, send SMS',
    'admin_reply_ticket_send_mail' => 'administrator reply ticket, send mail',
    'admin_reply_ticket_send_sms' => 'Administrator reply ticket, send SMS',
    'client_close_ticket_send_mail' => 'The customer closes the work order and sends an email',
    'client_close_ticket_send_sms' => 'The customer closes the work order and sends a text message',
    'client_reply_ticket_send_mail' => 'Customers reply to ticket and send emails',

    'product_is_not_exist' => 'Product does not exist',
    'ticket_upstream_host_is_not_exist' => 'Upstream product does not exist',
    'ticket_delivery_rule_error' => 'Transfer rules not met',
    'ticket_delivery_rule_blocked_words_error' => 'Ticket title contains blocked words, does not meet transfer rules',
    'ticket_supplier_not_support_delivery' => 'Supplier type does not support ticket delivery',
    'ticket_source_file_not_exist' => 'Ticket attachment does not exist',
    'ticket_network_desertion' => 'Network error',
    'log_ticket_delivery_success' => 'Ticket #{ticket_id} was successfully delivered to upstream #{upstream}#, related product #{host_id}',
    'log_ticket_delivery_success_admin' => 'Administrator #{admin} successfully passed the work order #{ticket_id} to upstream #{upstream}#, related product #{host_id}',
    'log_ticket_delivery_fail' => 'Work order #{ticket_id} failed to be passed to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',
    'log_ticket_delivery_fail_admin' => 'Administrator #{admin} failed to pass the work order #{ticket_id} to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',
    'log_ticket_delivery_upload_fail' => 'Work order #{ticket_id} failed to pass the work order attachment to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',
    'log_ticket_delivery_upload_fail_admin' => 'Administrator #{admin} failed to transfer the work order attachment #{ticket_id} to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',
    'ticket_has_deliveried' => 'The work order has been delivered and cannot be initiated repeatedly',
    'ticket_has_not_deliveried' => 'The work order has not been delivered and cannot be terminated',
    'ticket_delivery_status_1' => 'The work order has been enabled for delivery and cannot be repeated',
    'ticket_delivery_status_0' => 'The work order has been closed for delivery and cannot be repeated',
    'ticket_is_processing' => 'The work order is being processed',
    'ticket_log_client_processing_ticket' => '{client} processes the work order: {ticket_id}',
    'ticket_has_not_deliveried_not_reply' => 'The work order has not been delivered',
    'ticket_delivery_status_is_terminate' => 'The ticket has been terminated, and the ticket reply failed to be delivered',
    'log_ticket_reply_delivery_upload_fail_admin' => 'Administrator #{admin} failed to deliver the ticket reply #{ticket_reply_id} attachment to upstream #{upstream}#. The reason for failure is: {reason}, and the related product is #{host_id}',
    'log_ticket_reply_delivery_upload_fail' => 'The ticket reply #{ticket_reply_id} attachment to upstream #{upstream}# failed. The reason for failure is: {reason}, and the related product is #{host_id}',
    'log_ticket_reply_delivery_status_is_terminate_admin' => 'Administrator #{admin} failed to deliver ticket reply #{ticket_reply_id}, failure reason: {reason}, related product #{host_id}',
    'log_ticket_reply_delivery_status_is_terminate' => 'Delivery ticket reply #{ticket_reply_id} failed, failure reason: {reason}, related product #{host_id}',
    'log_ticket_reply_delivery_success' => 'The ticket reply #{ticket_reply_id} of ticket #{ticket_id} was delivered to upstream #{upstream}# successfully, related product #{host_id}',
    'log_ticket_reply_delivery_success_admin' => 'Administrator #{admin} successfully delivered the work order reply #{ticket_reply_id} of work order #{ticket_id} to upstream #{upstream}#, related product #{host_id}',
    'log_ticket_reply_delivery_fail' => 'Work order reply #{ticket_reply_id} of work order #{ticket_id} failed to be delivered to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',
    'log_ticket_reply_delivery_fail_admin' => 'Administrator #{admin} failed to deliver the work order reply #{ticket_reply_id} of work order #{ticket_id} to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',
    'log_ticket_urge_delivery_success' => 'Work order #{ticket_id} urge order delivery to upstream #{upstream} successfully, related product #{host_id}',
    'log_ticket_urge_delivery_fail' => 'Work order #{ticket_id} urge order delivery to upstream #{upstream} failed, failure reason: {reason}, related product #{host_id}',
    'log_ticket_close_delivery_success' => 'Work order #{ticket_id} close delivery to upstream #{upstream} successfully, related product #{host_id}',
    'log_ticket_close_delivery_fail' => 'Work order #{ticket_id} close delivery to upstream #{upstream} failed, failure reason: {reason}, related product #{host_id}',
    'ticket_upstream_cannot_operate' => 'The current work order has an upstream work order, and the delivery has been enabled, so it cannot be operated',
    'ticket_downstream_cannot_operate' => 'The current work order has a downstream work order, and the transfer has been enabled, so it cannot be operated',
    'ticket_log_admin_update_downstream_ticket_status_success' => 'Change the work order #{ticket} status #{status} to synchronize to downstream #{url} successfully',
    'ticket_log_admin_update_downstream_ticket_status_fail' => 'Change the work order #{ticket} status #{status} to synchronize to downstream #{url} failed, failure reason: {reason}',
    'ticket_push_token_error' => 'Work order signature error',
    'ticket_push_status_error' => 'Upstream push status error',

    'log_ticket_push_status_to_local_success' => 'Upstream #{upstream} pushes the status #{status} of work order #{ticket} to local successfully',
    'log_ticket_push_status_to_local_fail' => 'Upstream #{upstream} fails to push the status #{status} of work order #{ticket} to local, failure reason: {reason}',
    'log_ticket_push_reply_to_local_success' => 'Upstream #{upstream} pushes the reply #{ticket_reply_id} of work order #{ticket} to local successfully',
    'log_ticket_push_reply_to_local_fail' => 'Upstream #{upstream} fails to push the reply #{ticket_reply_id} of work order #{ticket} to local, failure reason: {reason}',
    'ticket_log_admin_update_downstream_ticket_reply_success' => 'Change work order #{ticket} reply #{ticket_reply_id} to synchronize to downstream #{url} successfully',
    'ticket_log_admin_update_downstream_ticket_reply_fail' => 'Change work order #{ticket} reply #{ticket_reply_id} to synchronize to downstream #{url} failed, failure reason: {reason}',
    'log_ticket_push_reply_delete_to_local_success' => 'Upstream #{upstream} pushes work order #{ticket} reply delete #{ticket_reply_id} to local successfully',
    'log_ticket_push_reply_delete_to_local_fail' => 'Failed to push the reply #{ticket_reply_id} of work order #{ticket} from upstream #{upstream} to local, failure reason: {reason}',
    'ticket_log_admin_update_downstream_ticket_reply_delete_success' => 'Successfully deleted the reply #{ticket_reply_id} of work order #{ticket} and synchronized it to downstream #{url}',
    'ticket_log_admin_update_downstream_ticket_reply_delete_fail' => 'Failed to delete the reply #{ticket_reply_id} of work order #{ticket} and synchronize it to downstream #{url}, failure reason: {reason}',
    'log_ticket_push_reply_create_to_local_success' => 'Successfully pushed the newly created reply #{ticket_reply_id} of work order #{ticket} from upstream #{upstream} to local',
    'log_ticket_push_reply_create_to_local_fail' => 'Failed to push the newly created reply #{ticket_reply_id} of the work order #{ticket} to the local, the reason for failure is: {reason}',
    'ticket_log_admin_update_downstream_ticket_reply_create_success' => 'The newly created reply #{ticket_reply_id} of the work order #{ticket} was synchronized to the downstream #{url} successfully',
    'ticket_log_admin_update_downstream_ticket_reply_create_fail' => 'The newly created reply #{ticket_reply_id} of the work order #{ticket} was synchronized to the downstream #{url} failed, the reason for failure is: {reason}',
    'log_ticket_delivery_terminate_success' => 'Administrator #{admin} terminated the delivery of work order #{ticket_id} to upstream #{upstream}# successfully, related product #{host_id}',
    'log_ticket_delivery_terminate_fail' => 'Administrator #{admin} failed to terminate the delivery of work order #{ticket_id} to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',
    'log_ticket_delivery_active_success' => 'Administrator #{admin} started the delivery of work order #{ticket_id} to upstream #{upstream}# successfully, related product #{host_id}',
    'log_ticket_delivery_active_fail' => 'Administrator #{admin} failed to start the delivery of work order #{ticket_id} to upstream #{upstream}#, failure reason: {reason}, related product #{host_id}',

    'ticket_upstream_admin' => 'Upstream Admin',
    'ticket_downstream_admin' => 'Downstream Admin',
];
