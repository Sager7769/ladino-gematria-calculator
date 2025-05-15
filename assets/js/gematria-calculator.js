/**
 * קוד JavaScript למחשבון גימטריה
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // הגדרת ערכי גימטריה
        const gematriaValues = {
            'א': 1,
            'ב': 2,
            'ג': 3,
            'ד': 4,
            'ה': 5,
            'ו': 6,
            'ז': 7,
            'ח': 8,
            'ט': 9,
            'י': 10,
            'כ': 20,
            'ל': 30,
            'מ': 40,
            'נ': 50,
            'ס': 60,
            'ע': 70,
            'פ': 80,
            'צ': 90,
            'ק': 100,
            'ר': 200,
            'ש': 300,
            'ת': 400,
        };
        
        // פונקציה לחישוב גימטריה
        function calculateGematria(text) {
            let gematria = 0;
            
            for (let i = 0; i < text.length; i++) {
                const letter = text[i];
                if (gematriaValues[letter]) {
                    gematria += gematriaValues[letter];
                }
            }
            
            // הוספת 1240 לתוצאה
            gematria += 1240;
            
            return gematria;
        }
        
        // פונקציה לעדכון התוצאה
        function updateResult(text, gematria) {
            const resultElement = $('#gematria-calculator-result');
            resultElement.empty();
            
            const resultHTML = `
                <span class="gematria-year">השנה הלועזית של '${text}' היא: </span>
                <span class="gematria-number gematria-positive">${gematria}</span>
            `;
            
            resultElement.html(resultHTML);
            $('#gematria-calculator-error').empty();
        }
        
        // טיפול באירוע לחיצה על כפתור החישוב
        $('#gematria-calculator-button').on('click', function() {
            const text = $('#gematria-calculator-text').val();
            
            if (!text) {
                $('#gematria-calculator-error').text('אנא הזן טקסט בעברית');
                return;
            }
            
            // אפשרות 1: חישוב מקומי (בצד הלקוח)
            const gematria = calculateGematria(text);
            updateResult(text, gematria);
            
            // אפשרות 2: שליחת בקשת AJAX לחישוב בצד השרת
            /*
            $.ajax({
                url: gematria_calculator_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'calculate_gematria',
                    nonce: gematria_calculator_vars.nonce,
                    text: text
                },
                success: function(response) {
                    if (response.success) {
                        updateResult(response.data.text, response.data.gematria);
                    } else {
                        $('#gematria-calculator-error').text('שגיאה: ' + response.data);
                    }
                },
                error: function() {
                    $('#gematria-calculator-error').text('שגיאה בשליחת הבקשה');
                }
            });
            */
        });
        
        // טיפול באירוע לחיצה על כפתור הניקוי
        $('#gematria-calculator-clear').on('click', function() {
            // ניקוי שדה הטקסט
            $('#gematria-calculator-text').val('');
            
            // ניקוי אזור התוצאה
            $('#gematria-calculator-result').empty();
            
            // ניקוי הודעות שגיאה
            $('#gematria-calculator-error').empty();
            
            // מיקוד בשדה הטקסט
            $('#gematria-calculator-text').focus();
        });
    });
})(jQuery);