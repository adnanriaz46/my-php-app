const glEmailTemplates = [
    // unsubscribe
    //unsubscribe_preferences
    {
        verify_email: {
            "name": "Zainh Devid",
            "verification_url": "",
            "contact_support": "",
        }
    }, {
        password_change: {
            "name": "Zainh Devid",
            "contact_support": "",
            "password_url": ""
        }
    },
    {
        moved_off_market: {
            "subject": "Action Required [Moved to Off-Market] - 1837 North 28th Street, Philadelphia, PA 19121",
            "property_id": "2188537",
            "property_address": "1837 North 28th Street, Philadelphia, PA 19121",
            "property_image": "https://wholesale-images.s3.amazonaws.com/2188537/awsS3_bucket_173876490459598.webp",
            "message": "Your property has been moved to Temporary Off Market status. Please log in to your account and reactivate your property at your earliest convenience.",
            "info": "Make sure to update the status of your property if it has changed. When you have an active wholesale listing posted, you must log into your account at least once every 3 days or your listing will automatically be moved to temp off market status",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        static_alert: {
            "subject": "Subjct... ",
            "property_id": "2188537",
            "property_address": "1837 North 28th Street, Philadelphia, PA 19121, USA",
            "property_image": "https://wholesale-images.s3.amazonaws.com/2188537/awsS3_bucket_173876490459598.webp",
            "total_views": "20",
            "address_requests": "40",
            "questions": "3",
            "offers": "0",
            "showings": "1",
            "info": "Make sure to update the status of your property if it has changed. When you have an active wholesale listing posted, you must log into your account at least once every 3 days or your listing will automatically be moved to temp off market status",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        instant_offer: {
            "subject": "Subjct... ",
            "property_id": "2188537",
            "property_address": "1837 North 28th Street, Philadelphia, PA 19121, USA",
            "property_image": "https://wholesale-images.s3.amazonaws.com/2188537/awsS3_bucket_173876490459598.webp",
            "contractual_name": "D.S.John",
            "name": "Jonh Walter",
            "phone": "9847872888",
            "email": "john.walter212@gmail.com",
            "offer_price": "$333,000",
            "deposit_amount": "$90,000",
            "closing": "03/21/2025",
            "agent_involved": "Yes",
            "agent_name": "Ag. Colin",
            "agent_email": "reql.colin122@gmail.com",
            "agent_commission": "$10,000",
            "note": "I just want to check it out the property, for further decisions",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        ask_question: {
            "subject": "Subjct... ",
            "property_id": "2188537",
            "property_address": "1837 North 28th Street, Philadelphia, PA 19121, USA",
            "property_image": "https://wholesale-images.s3.amazonaws.com/2188537/awsS3_bucket_173876490459598.webp",
            "name": "Jonh Walter",
            "phone": "9847872888",
            "email": "john.walter212@gmail.com",
            "question": "I just want to check it out the property, for further decisions",
            "preffered_contact_via": "Email",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        schedule_showing: {
            "subject": "Subjct... ",
            "property_id": "2188537",
            "property_address": "1837 North 28th Street, Philadelphia, PA 19121, USA",
            "property_image": "https://wholesale-images.s3.amazonaws.com/2188537/awsS3_bucket_173876490459598.webp",
            "preferred_time": "Wed 02/23/2025 08:30PM",
            "name": "Jonh Walter",
            "phone": "9847872888",
            "email": "john.walter212@gmail.com",
            "message": "I just want to check it out the property, for further decisions",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        on_live: {
            "subject": "Subjct... ",
            "status": "Active",
            "dom": "12",
            "deal_type": "MLS",
            "list_price": "350,000",
            "office_info": "John Doe at Mantion right",
            "address": "208 Hermitage Dr, Wayne PA 19087",
            "beds": "2",
            "baths": "1.5",
            "total_sqft": "1,200",
            "rent": "200.00",
            "avm": "605,028",
            "arv": "800,172",
            "image": "//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1700084508086x910347355422074000/802596026656_1024_768_WM_TP6C3JU4YVMP6nYm.jpg",
            "url": "https://revamp365.ai/version-test/?recordid=1704263",
            "est_cash_flow": "-4,732.61",
            "delta_psf": "292.52",
            "est_flip_profit": "93,900",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        assessment_drip: {
            "subject": "Subjct... ",
            "body": "html",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        common_email: {
            'subject': '',
            "body": "html",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        email_marketing: {
            "county": "Delaware County",
            "state": "PA",
            "property_url": "https://revamp365.ai/?recordid=1833477",
            "city_state_zip": "Woodlyn PA 19094",
            "price": "249k",
            "property_img": "https://files.constantcontact.com/9e418877701/d48b5473-9950-4f44-acd2-f45e1c2d6b5c.png",
            "user_description": "Upper Darby row home ready for some cosmetic upgrades. House is in great shape overall, just outdated. Keep as a rental or do a quick flip. Property is currently Vacant and on lockbox. Reach out today.\n\nProperty Details:\n\nBed: 3\nBath: 1\nSqft: 1,339\nLot Size Sqft: 1,699\nYear Built: 1928\nNatural Gas - radiant heat\nNo central air\nSewer & municipal water\nTaxes: $1,105\n\nComps:\n\n352 Huntley Rd, Upper Darby, PA 19082 Sold $231,000 on 02/16/24\n315 Avon Rd, Upper Darby, PA 19082 Sold $223,450 on 06/12/23\n325 Hampden Rd, Upper Darby, PA 19082 Sold $230,000 on 10/09/23\n\nPROPERTY IS VACANT & ON LOCKBOX. REACH OUT TODAY 610-202-5440",
            "user_img": "https://files.constantcontact.com/9e418877701/44f2de51-cb62-47d8-985f-dd09a830fa4d.jpg",
            "user_name": "Drew Farnese",
            "user_company": "Revamp 365",
            "user_phone": "215-796-8712",
            "user_email": "drew@revamp365.net",
            "user_co_address": "1309 MacDade Blvd | Woodlyn, PA 19094 US",
            "beds": "3",
            "baths": "1",
            "sqft": "1,339",
            "subject": "test email subbject",
            "header": "New off-market deal in Delaware County, PA",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/?unsub=true"
        }
    },
    {
        property_saved_alert: {
            "subject": "this just test",
            "status": "Active",
            "dom": "12",
            "deal_type": "MLS",
            "list_price": "350,000",
            "office_info": "John Doe at Mantion right",
            "address": "208 Hermitage Dr, Wayne PA 19087",
            "beds": "2",
            "baths": "1.5",
            "total_sqft": "1,200",
            "rent": "200,990",
            "avm": "605,028",
            "arv": "800,172",
            "image": "//119e394564295cd17eff6c1a28e68b43.cdn.bubble.io/f1700084508086x910347355422074000/802596026656_1024_768_WM_TP6C3JU4YVMP6nYm.jpg",
            "url": "https://revamp365.ai/version-test/?recordid=1704263",
            "alert_name": "New saved search 001",
            "property_count": "89",
            "est_cash_flow": "-4,732.61",
            "delta_psf": "292.52",
            "est_flip_profit": "93,900",
            "unsubscribe": "https://revamp365.ai/version-test/email_preference/1696595786523x185221497176014430"
        }
    },
]
