--

-- Fix zip_code format

--

update users_addresses
set
    zip_code = regexp_replace(zip_code, '[^0-9]+', '');

--

-- Fix document and phone format

--

update users
set
    document = regexp_replace(document, '[^0-9]+', ''),
    phone = regexp_replace(phone, '[^0-9]+', '');