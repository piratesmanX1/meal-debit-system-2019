# meal-debit-system-2019
 
A group assignment which being assigned at the last semester of Diploma, which is an assessment similar to the Final Year Project (FYP) from Degree as the course required us to submit a full progress report every week on our system. The assessment required an abundance of documentation to record down every single element of the system, which is a horrendous task that forced our hands at the beginning of the final semester. In this assignment we chose to do a website that focused on handling meal transaction within the school environment. By implementing the website with the additional skill that I've learned such as AJAX, it made the overall system improved a lot compared to the Reise Web-Portal project which has been done in 2018. The website also equipped with a higher level of SQL knowledge as well and making the server process even more fluent. Bootstrap 4 was also being learned from this assessment as well, and making the overall system looks even more professional.

**The uploaded project file is only for references and comparison, the project file will not be used in any profit-oriented activities without my permission. The user that downloaded the said project will be responsible for any outcomes of their future implementation on the project, and I will not held any legal responsibilities upon your action. However, any users are welcomed to suggest any changes or improvements upon the project if they want to.**

**===================================INSTRUCTIONS===================================**
1. Import Database into phpMyAdmin. (File name: ```meal_debit_system.sql```)
2. Go to SQL tab in phpMyAdmin and type this (before that enable event scheduler, type in SQL tab with this: ```SET GLOBAL event_scheduler="ON"; ```):

```
CREATE DEFINER=`root`@`localhost` EVENT `restock_meal` ON SCHEDULE EVERY 1 MONTH STARTS '2019-01-31 23:59:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Automatically update the database when the meal restock-ed.' DO UPDATE meal 
SET meal_additional_quantity = meal.meal_quantity, meal_quantity = (meal.meal_quantity + meal.meal_default_quantity)
```

(P.S: Do not type all in SQL. 1 by 1, otherwise will failed.)

```
CREATE DEFINER=`root`@`localhost` EVENT `generate_report` ON SCHEDULE EVERY 1 MONTH STARTS '2019-01-31 23:58:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Automatically generates Monthly Report at the end of the month.' DO INSERT INTO monthly_report
(report_name, meal_id, meal_name, meal_cost, meal_quantity_total, meal_brand, meal_cost_total, month_report, year_report, generated_time)
SELECT MONTHNAME(CURRENT_TIMESTAMP()), meal_id, meal_name, meal_price, ((meal_additional_quantity + meal_default_quantity) - meal_quantity),
meal_brand_id, (((meal_additional_quantity + meal_default_quantity) - meal_quantity) * meal_price), MONTH(CURRENT_TIMESTAMP()), YEAR(CURRENT_TIMESTAMP()), CURRENT_TIMESTAMP()
FROM meal
```

3. Download the files, put all the files into the folder named ```SDP``` inside it and place it in ```www``` in WAMP.

with the directory like this: ```www\APU\SDP\homepage.html```

4. Change some setting in WAMP to let HTML run the PHP code as well: 

Follow the instruction in this link:
https://stackoverflow.com/questions/11312316/how-do-i-add-php-code-file-to-html-html-files

**===================================INSTRUCTIONS===================================**
