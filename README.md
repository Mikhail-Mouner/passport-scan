## Document Scanning Feature

This application includes automatic document scanning and data extraction for passports, ID cards, and driving licenses from all nationalities.

### Setup

1. **Get ID Analyzer API Key**: Sign up at [ID Analyzer](https://www.idanalyzer.com) and get your API key.

2. **Environment Configuration**: Add your API key to `.env`:
   ```env
   IDANALYZER_API_KEY=your_api_key_here
   IDANALYZER_REGION=US  # or EU
   ```

3. **Install Dependencies**:
   ```bash
   composer install
   php artisan storage:link
   php artisan migrate
   ```

### Usage

Upload a document image and specify the document type:

```php
// POST /posts/process
{
    "image": "path/to/document.jpg",
    "document_type": "passport" // or "id" or "driving_license"
}
```

The system will:
- Extract text and data from the document
- Verify document authenticity
- Auto-fill database with extracted information
- Return structured data for immediate use

### Supported Document Types

- **Passports**: All nationalities (98% global coverage)
- **ID Cards**: National identity cards worldwide
- **Driving Licenses**: Driver licenses from all countries

### Extracted Data Fields

- Full name, first name, last name
- Document number
- Date of birth, expiry, issue
- Gender, nationality, address
- Document authenticity verification
- Face match verification (if selfie provided)

### API Response

```json
{
    "message": "Document processed and data saved successfully",
    "document_id": 1,
    "extracted_data": {
        "first_name": "John",
        "last_name": "Doe",
        "document_number": "P123456789",
        "is_authentic": true,
        "face_match": true
    }
}
```
