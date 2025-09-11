# Wholesale Property API Documentation

## Overview
This API provides access to wholesale properties with minimal authentication using an API key.

## Authentication
All API requests require an API key provided via:
- Header: `X-API-Key: your-api-key`
- Query parameter: `?api_key=your-api-key`

Set your API key in `.env`:
```
WHOLESALE_API_KEY=your-secure-api-key-here
```

## Base URL
```
https://your-domain.com/api/v1/wholesale
```

## Endpoints

### 1. Get Properties
**GET** `/properties`

Get all approved and active wholesale properties with filtering and pagination.

**Query Parameters:**
- `county` - Filter by county
- `city` - Filter by city (partial match)
- `state` - Filter by state
- `zip` - Filter by zip code
- `min_price` - Minimum list price
- `max_price` - Maximum list price
- `beds` - Minimum number of bedrooms
- `baths` - Minimum number of bathrooms
- `structure_type` - Filter by structure type
- `sort_by` - Sort field (default: created_at)
- `sort_order` - Sort direction: asc/desc (default: desc)
- `per_page` - Items per page (max: 100, default: 20)
- `page` - Page number

**Example:** 
```bash
curl -H "X-API-Key: your-api-key" \
  "https://your-domain.com/api/v1/wholesale/properties?county=Harris&min_price=100000&max_price=500000&per_page=10"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "lat": "29.7604",
      "lng": "-95.3698",
      "beds": "3",
      "baths": "2.0",
      "structure_type": "Single Family",
      "total_finished_sqft": 1500,
      "list_price": 250000,
      "full_street_address": "123 Main St",
      "city_name": "Houston",
      "state_or_province": "TX",
      "zip_code": "77001",
      "county": "Harris",
      "images": ["url1", "url2"],
      "seller_arv": 300000,
      "seller_est_flip_profit": 50000,
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 50,
    "from": 1,
    "to": 10
  }
}
```

### 2. Get Property by ID
**GET** `/properties/{id}`

Get a specific property by its ID.

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "https://your-domain.com/api/v1/wholesale/properties/123"
```

### 3. Get Property by Slug
**GET** `/properties/slug/{slug}`

Get a specific property by its slug.

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "https://your-domain.com/api/v1/wholesale/properties/slug/property-slug-here"
```

### 4. Search Properties
**GET** `/properties/search`

Search properties by address, city, county, or zip code.

**Query Parameters:**
- `query` - Search term (required, min 3 characters)

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "https://your-domain.com/api/v1/wholesale/properties/search?query=Houston"
```

### 5. Get Counties
**GET** `/counties`

Get list of all available counties.

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "https://your-domain.com/api/v1/wholesale/counties"
```

**Response:**
```json
{
  "success": true,
  "data": ["Harris", "Fort Bend", "Montgomery"]
}
```

### 6. Get Cities by County
**GET** `/cities`

Get list of cities for a specific county.

**Query Parameters:**
- `county` - County name (required)

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "https://your-domain.com/api/v1/wholesale/cities?county=Harris"
```

### 7. Get Statistics
**GET** `/stats`

Get overall statistics about wholesale properties.

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "https://your-domain.com/api/v1/wholesale/stats"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_properties": 150,
    "avg_price": 275000,
    "min_price": 50000,
    "max_price": 750000,
    "counties_count": 8
  }
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Invalid or missing API key"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Property not found or not available"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "query": ["The query field is required."]
  }
}
```

## Rate Limiting
Currently no rate limiting is implemented, but may be added in the future.

## Data Fields
Properties include all fields from the `WholesaleProperty` model:
- Basic info: beds, baths, sqft, year_built, etc.
- Location: address, city, state, zip, county, coordinates
- Financial: list_price, seller_arv, seller_est_flip_profit, etc.
- Images: array of image URLs
- Status and approval flags
- Timestamps: created_at, updated_at
