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
    t.index ["product_id"], name: "carts_product_id_index"
    t.index ["user_id"], name: "carts_user_id_index"
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
    t.index ["category_id"], name: "category_products_category_id_index"
    t.index ["parent_id"], name: "category_products_parent_id_index"
    t.index ["product_id"], name: "category_products_product_id_index"
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
    t.index ["id"], name: "email_formats_id_index"
    t.index ["type"], name: "email_formats_type_index"
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
    t.index ["id"], name: "email_histories_id_index"
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
    t.index ["id"], name: "email_templates_id_index"
    t.index ["type"], name: "email_templates_type_index"
  end

  create_table "failed_jobs", force: :cascade do |t|
    t.bigint "id"
    t.string "uuid", limit: 191
    t.text "connection"
    t.text "queue"
    t.text "payload"
    t.text "exception"
    t.datetime "failed_at"
    t.index ["uuid"], name: "failed_jobs_uuid_unique", unique: true
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
    t.index ["id"], name: "import_csv_logs_id_index"
    t.index ["status"], name: "import_csv_logs_status_index"
    t.index ["user_id"], name: "import_csv_logs_user_id_index"
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
    t.index ["queue"], name: "jobs_queue_index"
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
    t.index ["milestone_id"], name: "milestone_histories_milestone_id_index"
    t.index ["order_id"], name: "milestone_histories_order_id_index"
    t.index ["updated_by"], name: "milestone_histories_updated_by_index"
    t.index ["user_id"], name: "milestone_histories_user_id_index"
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
    t.index ["user_id"], name: "oauth_access_tokens_user_id_index"
  end

  create_table "oauth_auth_codes", force: :cascade do |t|
    t.string "id", limit: 100
    t.bigint "user_id"
    t.bigint "client_id"
    t.text "scopes"
    t.string "revoked", limit: 1
    t.datetime "expires_at"
    t.index ["user_id"], name: "oauth_auth_codes_user_id_index"
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
    t.index ["user_id"], name: "oauth_clients_user_id_index"
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
    t.index ["access_token_id"], name: "oauth_refresh_tokens_access_token_id_index"
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
    t.index ["order_id"], name: "order_products_order_id_index"
    t.index ["product_id"], name: "order_products_product_id_index"
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
    t.index ["order_id"], name: "order_statuses_order_id_index"
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
    t.index ["user_id"], name: "orders_user_id_index"
  end

  create_table "password_reset_tokens", force: :cascade do |t|
    t.integer "id"
    t.string "email", limit: 191
    t.string "token", limit: 191
    t.datetime "created_at"
    t.datetime "updated_at"
    t.index ["id"], name: "password_resets_id_index"
  end

  create_table "permission_role", force: :cascade do |t|
    t.integer "permission_id"
    t.integer "role_id"
    t.index ["permission_id"], name: "permission_role_permission_id_index"
    t.index ["role_id"], name: "permission_role_role_id_index"
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
    t.index ["product_id"], name: "product_galleries_product_id_index"
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
    t.index ["category_id"], name: "product_histories_category_id_index"
    t.index ["sub_category_id"], name: "product_histories_sub_category_id_index"
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
    t.index ["category_id"], name: "products_category_id_index"
    t.index ["sub_category_id"], name: "products_sub_category_id_index"
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
    t.index ["bucket", "period", "type", "aggregate", "key_hash"], name: "pulse_aggregates_bucket_period_type_aggregate_key_hash_unique", unique: true
    t.index ["period", "bucket"], name: "pulse_aggregates_period_bucket_index"
    t.index ["period", "type", "aggregate", "bucket"], name: "pulse_aggregates_period_type_aggregate_bucket_index"
    t.index ["type"], name: "pulse_aggregates_type_index"
  end

  create_table "pulse_entries", force: :cascade do |t|
    t.bigint "id"
    t.integer "timestamp"
    t.string "type", limit: 191
    t.string "key"
    t.string "key_hash", limit: 16
    t.bigint "value"
    t.index ["key_hash"], name: "pulse_entries_key_hash_index"
    t.index ["timestamp", "type", "key_hash", "value"], name: "pulse_entries_timestamp_type_key_hash_value_index"
    t.index ["timestamp"], name: "pulse_entries_timestamp_index"
    t.index ["type"], name: "pulse_entries_type_index"
  end

  create_table "pulse_values", force: :cascade do |t|
    t.bigint "id"
    t.integer "timestamp"
    t.string "type", limit: 191
    t.string "key"
    t.string "key_hash", limit: 16
    t.string "value"
    t.index ["timestamp"], name: "pulse_values_timestamp_index"
    t.index ["type", "key_hash"], name: "pulse_values_type_key_hash_unique", unique: true
    t.index ["type"], name: "pulse_values_type_index"
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
    t.index ["batch_id"], name: "telescope_entries_batch_id_index"
    t.index ["created_at"], name: "telescope_entries_created_at_index"
    t.index ["family_hash"], name: "telescope_entries_family_hash_index"
    t.index ["type", "should_display_on_index"], name: "telescope_entries_type_should_display_on_index_index"
    t.index ["uuid"], name: "telescope_entries_uuid_unique", unique: true
  end

  create_table "telescope_entries_tags", force: :cascade do |t|
    t.string "entry_uuid", limit: 36
    t.string "tag", limit: 191
    t.index ["entry_uuid", "tag"], name: "telescope_entries_tags_entry_uuid_tag_index"
    t.index ["tag"], name: "telescope_entries_tags_tag_index"
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
    t.index ["role_id"], name: "users_role_id_index"
  end

  # Add Foreign Key Constraints
  add_foreign_key "carts", "products", on_delete: :restrict
  add_foreign_key "carts", "users", on_delete: :restrict
  add_foreign_key "import_csv_logs", "users", on_delete: :restrict
  add_foreign_key "milestone_histories", "milestones"
  add_foreign_key "milestone_histories", "orders"
  add_foreign_key "milestone_histories", "users", column: "updated_by"
  add_foreign_key "milestone_histories", "users"
  add_foreign_key "order_products", "products", on_delete: :restrict
  add_foreign_key "orders", "users", on_delete: :restrict
  add_foreign_key "product_galleries", "products", on_delete: :restrict
  add_foreign_key "product_histories", "categories"
  add_foreign_key "products", "categories", on_delete: :restrict
  add_foreign_key "telescope_entries_tags", "telescope_entries", column: "entry_uuid", primary_key: "uuid", on_delete: :cascade
  add_foreign_key "users", "roles", on_delete: :restrict

end