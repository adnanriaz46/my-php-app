<?php

use Illuminate\Support\Facades\Route;
use App\Helper\DBApiHelper;

Route::get('data-db-api/get-calculated-values', [DBApiHelper::class, 'getCalculatedData'])
    ->name('data-db-api.get_calculated_values');

Route::get('data-db-api/get-mls-history', [DBApiHelper::class, 'getMLSHistoryData'])
    ->name('data-db-api.get_mls_history');

Route::get('data-db-api/get-avg-comps', [DBApiHelper::class, 'getAveragePropertyCompsData'])
    ->name('data-db-api.get_avg_comps');

Route::get('data-db-api/get-property-minimal-list-by-text', [DBApiHelper::class, 'getPropertyMinimalListByText'])
    ->name('data-db-api.get_property_minimal_list_by_text');

Route::post('data-db-api/get-property-list', [DBApiHelper::class, 'getPropertyListData'])
    ->name('data-db-api.get_property_list');

Route::post('data-db-api/get-property-data', [DBApiHelper::class, 'getPropertyData'])
    ->name('data-db-api.get_property');

Route::post('data-db-api/get-property-count', [DBApiHelper::class, 'getPropertyCount'])
    ->name('data-db-api.get_property_count');

Route::get('data-db-api/get-ask-ai', [DBApiHelper::class, 'getAskAiContent'])
    ->name('data-db-api.get_ask_ai');

Route::get('data-db-api/search-buyers', [DBApiHelper::class, 'searchBuyers'])
    ->name('data-db-api.search_buyers');

Route::get('data-db-api/get-ownership-data', [DBApiHelper::class, 'getOwnershipData'])
    ->name('data-db-api.get_ownership_data');


Route::get('data-db-api/get-datatree-property-info', [DBApiHelper::class, 'getDatatreePropertyInfo'])
    ->name('data-db-api.get_datatree_property_info');

Route::get('data-db-api/get-sold-under-market-value', [DBApiHelper::class, 'getSoldUnderMarketValue'])
    ->name('data-db-api.get_sold_under_market_value');

Route::get('data-db-api/get-sales-activity', [DBApiHelper::class, 'getSalesActivity'])
    ->name('data-db-api.get_sales_activity');

Route::get('data-db-api/get-status-activity', [DBApiHelper::class, 'getStatusActivity'])
    ->name('data-db-api.get_status_activity');




