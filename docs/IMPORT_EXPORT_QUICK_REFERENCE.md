# Import/Export Quick Reference

## Export Attendance Data

### Via Admin Panel
1. Navigate to **Admin → Export → Attendance**
2. Apply filters (date range, name) if needed
3. Select format: CSV, Excel, or PDF
4. Click **Export** or **Email Export**

### Exported CSV Columns
```
full_name, attendance, date_time, location, shift_type, source_signature, source_payload
```

### Example Export Row
```
Juan Dela Cruz, Time in, 2026-05-18 09:00:00, Main Office, Morning, sig_abc123, {"sheet_id":"123","row":5}
```

---

## Import Attendance Data

### Via Admin Panel
1. Navigate to **Admin → Import**
2. Select **Attendance** as import type
3. Upload CSV or Excel file
4. Review preview
5. Click **Import**

### Required Columns (Minimum)
```
full_name, attendance, date_time
```

### Optional Columns
```
location, shift_type, source_signature, source_payload
```

### Example Import CSV (Minimal)
```
full_name,attendance,date_time
Juan Dela Cruz,Time in,2026-05-18 09:00:00
Juan Dela Cruz,Time out,2026-05-18 17:00:00
```

### Example Import CSV (Full)
```
full_name,attendance,date_time,location,shift_type,source_signature,source_payload
Juan Dela Cruz,Time in,2026-05-18 09:00:00,Main Office,Morning,sig_abc123,"{""sheet_id"":""123"",""row"":5}"
Juan Dela Cruz,Time out,2026-05-18 17:00:00,Main Office,Morning,sig_abc124,"{""sheet_id"":""123"",""row"":6}"
```

---

## Export → Re-import Workflow

### Step 1: Export
```
Admin → Export → Attendance → Select filters → Export to CSV
```

### Step 2: Re-import
```
Admin → Import → Select file → Preview → Import
```

### Result
✅ All data preserved including:
- full_name
- attendance
- date_time
- location
- shift_type
- source_signature
- source_payload

---

## Field Descriptions

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `full_name` | String | ✅ Yes | Volunteer's full name (max 255 chars) |
| `attendance` | String | ✅ Yes | "Time in" or "Time out" |
| `date_time` | DateTime | ✅ Yes | Date and time (format: YYYY-MM-DD HH:MM:SS) |
| `location` | String | ❌ No | Work location (max 255 chars) |
| `shift_type` | String | ❌ No | Shift type (e.g., Morning, Afternoon) |
| `source_signature` | String | ❌ No | Unique identifier for source record (max 255 chars) |
| `source_payload` | JSON | ❌ No | Additional metadata as JSON string |

---

## Common Issues & Solutions

### Issue: "Invalid file type"
**Solution:** Use CSV, Excel (.xlsx), or TXT files only

### Issue: "Missing full_name"
**Solution:** Ensure all rows have a value in the full_name column

### Issue: "Invalid date_time format"
**Solution:** Use format: `YYYY-MM-DD HH:MM:SS` (e.g., `2026-05-18 09:00:00`)

### Issue: "source_payload validation failed"
**Solution:** Ensure source_payload is valid JSON or leave it empty

### Issue: "Duplicate record"
**Solution:** Records with same full_name and date_time are skipped to prevent duplicates

---

## Data Validation Rules

### On Import
- `full_name`: Required, max 255 characters
- `attendance`: Required, any string value
- `date_time`: Required, valid datetime format
- `location`: Optional, max 255 characters
- `shift_type`: Optional, max 255 characters
- `source_signature`: Optional, max 255 characters, must be unique
- `source_payload`: Optional, must be valid JSON if provided

### Duplicate Detection
Records are considered duplicates if:
- Same `full_name` AND
- Same `date_time` (date only, not time)

Duplicates are skipped during import to prevent data duplication.

---

## Tips for Best Results

1. **Always export before bulk import** - Keep a backup of original data
2. **Use consistent date formats** - YYYY-MM-DD HH:MM:SS works best
3. **Validate source_payload JSON** - Use online JSON validators if unsure
4. **Check for duplicates** - Review import preview before confirming
5. **Keep source_signature unique** - Helps track data origin
6. **Test with small dataset first** - Before importing large files

---

## Template Download

Download the import template from **Admin → Import → Download Template**

This provides the correct column structure and example data format.
