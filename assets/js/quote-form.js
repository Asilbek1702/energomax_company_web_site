/**
 * Auto-populate product_name on quote forms from product card/page.
 * No jQuery dependency — vanilla fetch for REST submissions.
 */
(function () {
    'use strict';

    function getProductName() {
        var body = document.body;
        if (body && body.dataset.productName) {
            return body.dataset.productName;
        }

        var params = new URLSearchParams(window.location.search);
        if (params.get('product')) {
            return params.get('product');
        }

        var titleEl = document.querySelector('.energomax-page-title, h1.entry-title, h1');
        if (titleEl && document.body.classList.contains('single-energomax_product')) {
            return titleEl.textContent.trim();
        }

        return '';
    }

    function populateProductNameField() {
        var productName = getProductName();
        if (!productName) {
            return;
        }

        var fields = document.querySelectorAll(
            'input[name="product_name"], .energomax-product-name-field'
        );

        fields.forEach(function (field) {
            if (!field.value) {
                field.value = productName;
            }
        });
    }

    function bindRestQuoteForm(form) {
        if (!form || form.dataset.energomaxBound) {
            return;
        }

        form.dataset.energomaxBound = '1';

        form.addEventListener('submit', function (event) {
            if (!form.classList.contains('energomax-rest-quote-form')) {
                return;
            }

            event.preventDefault();

            var restConfig = window.energomaxRest || {};
            var messageEl = form.querySelector('.energomax-form-message');
            var submitBtn = form.querySelector('[type="submit"]');

            if (submitBtn) {
                submitBtn.disabled = true;
            }

            var payload = {
                name: (form.querySelector('[name="name"]') || {}).value || '',
                phone: (form.querySelector('[name="phone"]') || {}).value || '',
                email: (form.querySelector('[name="email"]') || {}).value || '',
                product_name: (form.querySelector('[name="product_name"]') || {}).value || '',
                comment: (form.querySelector('[name="comment"]') || {}).value || '',
                nonce: restConfig.nonce || ''
            };

            fetch(restConfig.root + '/quote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': restConfig.nonce || ''
                },
                body: JSON.stringify(payload)
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    if (messageEl) {
                        messageEl.textContent = result.ok
                            ? (result.data.message || 'Thank you!')
                            : (result.data.message || 'Error sending request.');
                        messageEl.className = 'energomax-form-message ' + (result.ok ? 'success' : 'error');
                        messageEl.style.display = 'block';
                    }
                    if (result.ok) {
                        form.reset();
                        populateProductNameField();
                    }
                })
                .catch(function () {
                    if (messageEl) {
                        messageEl.textContent = 'Network error. Please try again.';
                        messageEl.className = 'energomax-form-message error';
                        messageEl.style.display = 'block';
                    }
                })
                .finally(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        populateProductNameField();

        document.querySelectorAll('.energomax-rest-quote-form').forEach(bindRestQuoteForm);
    });
})();
