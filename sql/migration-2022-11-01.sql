alter table
    `articles_integrations_services`
add
    column `status_checked_at` datetime null default null after `service_response_payload`;