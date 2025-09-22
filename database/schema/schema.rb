ActiveRecord::Schema[8.0].define(version: 2025_09_12_082651) do

  # Converted from MySQL schema

  create_table "activity_logs", force: :cascade do |t|
    t.integer "id"
    t.string "log_name", limit: 191
    t.text "description"
    t.string "ip_address", limit: 191
    t.string "subject_type", limit: 191
    t.integer "subject_id"
    t.string "causer_type", limit: 191
    t.integer "causer_id"
    t.string "response_type"
    t.text "properties"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "carts", force: :cascade do |t|
    t.integer "id"
    t.integer "user_id"
    t.integer "product_id"
    t.integer "quantity"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "categories", force: :cascade do |t|
    t.integer "id"
    t.integer "parent_id"
    t.string "name", limit: 191
    t.integer "price"
    t.text "description"
    t.string "category_status"
    t.integer "min_order_quantity"
    t.string "featured_image", limit: 500
    t.integer "left_over_stock_number"
    t.string "unit_type"
    t.integer "set_unit"
    t.decimal "no_of_meter_per_unit"
    t.integer "unit_price"
    t.decimal "discount"
    t.integer "without_discount_unit_price"
    t.decimal "without_discount_price"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "category_products", force: :cascade do |t|
    t.integer "product_id"
    t.integer "category_id"
    t.integer "parent_id"
  end

  create_table "email_formats", force: :cascade do |t|
    t.integer "id"
    t.string "type"
    t.string "label", limit: 100
    t.text "body"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
  end

  create_table "email_histories", force: :cascade do |t|
    t.integer "id"
    t.string "to_email", limit: 200
    t.string "subject", limit: 100
    t.text "body"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
  end

  create_table "email_templates", force: :cascade do |t|
    t.integer "id"
    t.string "type"
    t.string "label", limit: 100
    t.string "subject", limit: 100
    t.text "body"
    t.string "status"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
  end

  create_table "failed_jobs", force: :cascade do |t|
    t.bigint "id"
    t.string "uuid", limit: 191
    t.text "connection"
    t.text "queue"
    t.text "payload"
    t.text "exception"
    t.datetime "failed_at"
  end

  create_table "homebanners", force: :cascade do |t|
    t.integer "id"
    t.string "name", limit: 191
    t.string "featured_image", limit: 500
    t.string "banner_type", limit: 191
    t.string "banner_status"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "import_csv_logs", force: :cascade do |t|
    t.integer "id"
    t.string "file_name", limit: 255
    t.string "file_path", limit: 255
    t.string "model_name", limit: 255
    t.integer "user_id"
    t.string "status", limit: 1
    t.string "import_flag", limit: 1
    t.string "voucher_email", limit: 191
    t.string "redirect_link", limit: 191
    t.integer "no_of_rows"
    t.text "error_log"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.bigint "created_by"
    t.bigint "updated_by"
    t.bigint "deleted_by"
    t.datetime "deleted_at"
  end

  create_table "job_batches", force: :cascade do |t|
    t.string "id", limit: 191
    t.string "name", limit: 191
    t.integer "total_jobs"
    t.integer "pending_jobs"
    t.integer "failed_jobs"
    t.text "failed_job_ids"
    t.string "options"
    t.integer "cancelled_at"
    t.integer "created_at"
    t.integer "finished_at"
  end

  create_table "jobs", force: :cascade do |t|
    t.bigint "id"
    t.string "queue", limit: 191
    t.text "payload"
    t.string "attempts"
    t.integer "reserved_at"
    t.integer "available_at"
    t.integer "created_at"
  end

  create_table "migrations", force: :cascade do |t|
    t.integer "id"
    t.string "migration", limit: 191
    t.integer "batch"
  end

  create_table "milestone_histories", force: :cascade do |t|
    t.integer "id"
    t.integer "user_id"
    t.integer "milestone_id"
    t.integer "order_id"
    t.string "title", limit: 191
    t.decimal "price"
    t.string "image", limit: 500
    t.string "customer_name", limit: 191
    t.string "mobile_no", limit: 191
    t.decimal "bill_amount"
    t.string "status"
    t.integer "updated_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "milestones", force: :cascade do |t|
    t.integer "id"
    t.string "title", limit: 191
    t.decimal "price"
    t.string "milestone_image", limit: 500
    t.text "description"
    t.string "status"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "oauth_access_tokens", force: :cascade do |t|
    t.string "id", limit: 100
    t.bigint "user_id"
    t.bigint "client_id"
    t.string "name", limit: 191
    t.text "scopes"
    t.string "revoked", limit: 1
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "expires_at"
  end

  create_table "oauth_auth_codes", force: :cascade do |t|
    t.string "id", limit: 100
    t.bigint "user_id"
    t.bigint "client_id"
    t.text "scopes"
    t.string "revoked", limit: 1
    t.datetime "expires_at"
  end

  create_table "oauth_clients", force: :cascade do |t|
    t.bigint "id"
    t.bigint "user_id"
    t.string "name", limit: 191
    t.string "secret", limit: 100
    t.string "provider", limit: 191
    t.text "redirect"
    t.string "personal_access_client", limit: 1
    t.string "password_client", limit: 1
    t.string "revoked", limit: 1
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "oauth_personal_access_clients", force: :cascade do |t|
    t.bigint "id"
    t.bigint "client_id"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "oauth_refresh_tokens", force: :cascade do |t|
    t.string "id", limit: 100
    t.string "access_token_id", limit: 100
    t.string "revoked", limit: 1
    t.datetime "expires_at"
  end

  create_table "order_products", force: :cascade do |t|
    t.integer "id"
    t.integer "order_id"
    t.integer "product_id"
    t.string "product_name", limit: 191
    t.decimal "price"
    t.string "category_name", limit: 191
    t.string "featured_image", limit: 500
    t.integer "quantity"
    t.string "unit_type"
    t.integer "set_unit"
    t.decimal "no_of_meter_per_unit"
    t.integer "unit_price"
    t.decimal "discount"
    t.integer "without_discount_unit_price"
    t.decimal "without_discount_price"
    t.decimal "total_price"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "order_statuses", force: :cascade do |t|
    t.integer "id"
    t.integer "order_id"
    t.string "order_status"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "orders", force: :cascade do |t|
    t.integer "id"
    t.integer "user_id"
    t.integer "quantity"
    t.decimal "gst"
    t.decimal "without_gst_amount"
    t.decimal "payment_amount"
    t.string "order_status"
    t.text "order_status_remark"
    t.text "user_remark"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "password_reset_tokens", force: :cascade do |t|
    t.integer "id"
    t.string "email", limit: 191
    t.string "token", limit: 191
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "permission_role", force: :cascade do |t|
    t.integer "permission_id"
    t.integer "role_id"
  end

  create_table "permissions", force: :cascade do |t|
    t.integer "id"
    t.string "name", limit: 191
    t.string "guard_name", limit: 191
    t.string "label", limit: 191
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "product_galleries", force: :cascade do |t|
    t.integer "id"
    t.integer "product_id"
    t.string "gallery", limit: 191
    t.string "gallery_original", limit: 191
    t.string "gallery_thumbnail", limit: 191
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "product_histories", force: :cascade do |t|
    t.integer "id"
    t.string "name", limit: 191
    t.decimal "without_discount_price"
    t.decimal "price"
    t.decimal "category_price"
    t.text "description"
    t.string "item_code", limit: 191
    t.integer "category_id"
    t.string "available_status"
    t.string "status"
    t.integer "sub_category_id"
    t.integer "stock"
    t.integer "created_stock"
    t.integer "min_order_quantity"
    t.string "featured_image", limit: 500
    t.text "available_color"
    t.string "unit_type"
    t.integer "set_unit"
    t.integer "no_of_meter_per_unit"
    t.decimal "discount"
    t.integer "without_discount_unit_price"
    t.integer "unit_price"
    t.integer "is_arrival"
    t.integer "is_popular"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "products", force: :cascade do |t|
    t.integer "id"
    t.string "name", limit: 191
    t.decimal "without_discount_price"
    t.integer "price"
    t.decimal "category_price"
    t.text "description"
    t.string "item_code", limit: 191
    t.integer "category_id"
    t.string "available_status"
    t.string "status"
    t.integer "sub_category_id"
    t.integer "stock"
    t.integer "created_stock"
    t.integer "min_order_quantity"
    t.string "featured_image", limit: 500
    t.text "available_color"
    t.string "unit_type"
    t.integer "set_unit"
    t.decimal "no_of_meter_per_unit"
    t.decimal "discount"
    t.integer "without_discount_unit_price"
    t.integer "unit_price"
    t.integer "is_arrival"
    t.integer "is_popular"
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "pulse_aggregates", force: :cascade do |t|
    t.bigint "id"
    t.integer "bucket"
    t.string "period"
    t.string "type", limit: 191
    t.string "key"
    t.string "key_hash", limit: 16
    t.string "aggregate", limit: 191
    t.decimal "value"
    t.integer "count"
  end

  create_table "pulse_entries", force: :cascade do |t|
    t.bigint "id"
    t.integer "timestamp"
    t.string "type", limit: 191
    t.string "key"
    t.string "key_hash", limit: 16
    t.bigint "value"
  end

  create_table "pulse_values", force: :cascade do |t|
    t.bigint "id"
    t.integer "timestamp"
    t.string "type", limit: 191
    t.string "key"
    t.string "key_hash", limit: 16
    t.string "value"
  end

  create_table "roles", force: :cascade do |t|
    t.integer "id"
    t.string "name", limit: 191
    t.string "guard_name", limit: 191
    t.string "landing_page", limit: 191
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "sms_templates", force: :cascade do |t|
    t.integer "id"
    t.string "type", limit: 255
    t.string "label", limit: 255
    t.text "message"
    t.string "status", limit: 255
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
  end

  create_table "telescope_entries", force: :cascade do |t|
    t.bigint "sequence"
    t.string "uuid", limit: 36
    t.string "batch_id", limit: 36
    t.string "family_hash", limit: 191
    t.string "should_display_on_index", limit: 1
    t.string "type", limit: 20
    t.text "content"
    t.datetime "created_at"
  end

  create_table "telescope_entries_tags", force: :cascade do |t|
    t.string "entry_uuid", limit: 36
    t.string "tag", limit: 191
  end

  create_table "telescope_monitoring", force: :cascade do |t|
    t.string "tag", limit: 191
  end

  create_table "users", force: :cascade do |t|
    t.integer "id"
    t.integer "role_id"
    t.string "is_salesman"
    t.string "name", limit: 191
    t.string "company_name", limit: 191
    t.string "city", limit: 191
    t.string "contact_number", limit: 255
    t.string "email", limit: 255
    t.string "otp", limit: 191
    t.datetime "otp_verified_at"
    t.string "device_token", limit: 191
    t.string "user_status"
    t.string "password", limit: 191
    t.datetime "last_login"
    t.string "device_number", limit: 255
    t.integer "created_by"
    t.integer "updated_by"
    t.integer "deleted_by"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.datetime "deleted_at"
    t.string "remember_token", limit: 100
  end

end
