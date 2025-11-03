<!DOCTYPE html>
<html>
<head>
    <title>Test Image Upload</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select, textarea { margin: 5px 0 15px 0; padding: 8px; width: 300px; }
        button { padding: 10px 20px; margin: 10px 10px 10px 0; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>
    <h1>Test Store Visit Image Upload</h1>
    
    <form action="/test-image-handler" method="POST" enctype="multipart/form-data">
        <h3>Basic Info</h3>
        
        <label>Restaurant:</label>
        <input type="text" name="restaurant_name" value="Test Restaurant" required>
        
        <label>MIC:</label>
        <select name="mic" required>
            <option value="Morning">Morning</option>
            <option value="Afternoon">Afternoon</option>
            <option value="Evening">Evening</option>
        </select>
        
        <label>Visit Date:</label>
        <input type="date" name="visit_date" value="{{ date('Y-m-d') }}" required>
        
        <label>Purpose of Visit:</label>
        <input type="checkbox" name="purpose_of_visit[]" value="Routine Visit" checked> Routine Visit
        <input type="checkbox" name="purpose_of_visit[]" value="Quality Check" checked> Quality Check
        
        <h3>Question with Image (OCA Board Followed = No)</h3>
        
        <label>OCA Board Followed:</label>
        <select name="oca_board_followed" required>
            <option value="1">Yes</option>
            <option value="0" selected>No</option>
        </select>
        
        <label>Comment:</label>
        <textarea name="oca_board_comments" required>Board not properly followed - see attached image</textarea>
        
        <label>Upload Image(s):</label>
        <input type="file" name="question_images[oca_board_followed][]" accept="image/*" multiple>
        
        <input type="hidden" name="staff_know_duty" value="1">
        <input type="hidden" name="staff_know_duty_comments" value="Staff duty OK">
        <input type="hidden" name="coaching_directing" value="1">
        <input type="hidden" name="coaching_directing_comments" value="Coaching OK">
        <input type="hidden" name="smile_greetings" value="1">
        <input type="hidden" name="smile_greetings_comments" value="Greetings OK">
        <input type="hidden" name="suggestive_selling" value="1">
        <input type="hidden" name="suggestive_selling_comments" value="Selling OK">
        <input type="hidden" name="offer_promotion" value="1">
        <input type="hidden" name="offer_promotion_comments" value="Promotion OK">
        <input type="hidden" name="thank_direction" value="1">
        <input type="hidden" name="thank_direction_comments" value="Thank you OK">
        <input type="hidden" name="team_work_hustle" value="1">
        <input type="hidden" name="team_work_hustle_comments" value="Teamwork OK">
        <input type="hidden" name="order_accuracy" value="1">
        <input type="hidden" name="order_accuracy_comments" value="Accuracy OK">
        <input type="hidden" name="service_time" value="1">
        <input type="hidden" name="service_time_comments" value="Service OK">
        <input type="hidden" name="dine_in" value="1">
        <input type="hidden" name="dine_in_comments" value="Dine-in OK">
        <input type="hidden" name="take_out" value="1">
        <input type="hidden" name="take_out_comments" value="Takeout OK">
        <input type="hidden" name="external_clean" value="1">
        <input type="hidden" name="external_clean_comments" value="External OK">
        <input type="hidden" name="internal_clean" value="1">
        <input type="hidden" name="internal_clean_comments" value="Internal OK">
        <input type="hidden" name="kitchen_clean" value="1">
        <input type="hidden" name="kitchen_clean_comments" value="Kitchen OK">
        <input type="hidden" name="staff_grooming" value="1">
        <input type="hidden" name="staff_grooming_comments" value="Grooming OK">
        <input type="hidden" name="stock_fresh" value="1">
        <input type="hidden" name="stock_fresh_comments" value="Stock OK">
        <input type="hidden" name="cooling_equipment" value="1">
        <input type="hidden" name="cooling_equipment_comments" value="Cooling OK">
        <input type="hidden" name="manager_office" value="1">
        <input type="hidden" name="manager_office_comments" value="Office OK">
        <input type="hidden" name="updated_schedule" value="1">
        <input type="hidden" name="updated_schedule_comments" value="Schedule OK">
        <input type="hidden" name="food_waste" value="1">
        <input type="hidden" name="food_waste_comments" value="Waste OK">
        
        <input type="hidden" name="what_did_you_see" value="Test observation">
        <input type="hidden" name="why_had_issue" value="Test reason">
        <input type="hidden" name="how_to_improve" value="Test improvement">
        <input type="hidden" name="who_when_responsible" value="Test responsible">
        
        <br>
        <button type="submit" name="action" value="draft">Save as Draft</button>
        <button type="submit" name="action" value="submit">Submit</button>
    </form>
</body>
</html>
