# CSV Upload Requirements for Contact Import

## File Format
The CSV file must have exactly 9 columns in the following order:

1. **email** - Required, must be a valid email format
2. **tags** - Optional, comma-separated values (e.g., "member,vip,premium")
3. **phone_number** - Optional, will be cleaned of non-numeric characters
4. **first_name** - Optional
5. **last_name** - Optional
6. **counties_invest** - Optional, comma-separated county names
7. **deal_type** - Optional, comma-separated deal types (e.g., "MLS,Wholesale")
8. **zip_code_invest** - Optional, comma-separated zip codes
9. **price_range** - Optional, comma-separated price ranges

## Validation Rules

### Column Validation
- File must have exactly 9 columns in the specified order
- Column headers must match exactly: `email,tags,phone_number,first_name,last_name,counties_invest,deal_type,zip_code_invest,price_range`
- UTF-8 BOM characters are automatically removed

### Data Validation
- **Email**: Must be a valid email format, invalid emails will be skipped
- **Phone**: Automatically cleaned of non-numeric characters (except +, -, (, ), spaces)
- **Arrays**: Comma-separated values are automatically converted to arrays
- **Empty values**: Empty fields are converted to empty arrays for array fields

### Error Handling
- Invalid records are skipped and processing continues
- Error messages are logged with line numbers
- Success message includes counts of created, updated, and skipped records
- First few error messages are displayed to the user

## Example CSV Format

```csv
email,tags,phone_number,first_name,last_name,counties_invest,deal_type,zip_code_invest,price_range
john@example.com,"member,vip",(555) 123-4567,John,Doe,"Delaware,Adams",MLS,"19015,19035","20000,90000"
jane@test.com,"premium",+1-555-777-9999,Jane,Smith,"Chester,Montgomery",Wholesale,"19401,19403","50000,150000"
bob@email.com,,,Bob,Johnson,"Bucks,Philadelphia",MLS,"18901,18902","30000,80000"
```

## Processing Behavior

### New Contacts
- If email doesn't exist for the current user, a new contact is created
- All fields are populated with the provided data

### Existing Contacts
- If email already exists for the current user, the contact is updated
- Array fields (tags, counties, deal_type, zip, price_range) are merged with existing values
- Text fields (name, phone, first_name, last_name) are replaced with new values

### Performance
- Files are processed in batches of 1000 records
- Large files (up to 100MB) are supported
- Processing time limit is set to 10 minutes
- Memory limit is set to 2GB

## Error Scenarios

1. **Invalid column count**: File rejected with error message
2. **Wrong column order**: File rejected with error message
3. **Invalid email format**: Record skipped, processing continues
4. **Database errors**: Record skipped, error logged, processing continues
5. **File too large**: Upload rejected
6. **Invalid file type**: Upload rejected

## Success Response

The system will return a success message with:
- Number of contacts created
- Number of contacts updated
- Number of records skipped (if any)
- First few error messages (if any) 