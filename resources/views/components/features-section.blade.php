
<style>
    /* Scoped Reset for Features Section */
    .features-section * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }

    /* Features Section */
    .features-section {
        padding: 60px 0;
        background: #ffffff; /* Solid white background for clarity */
        position: relative; /* Ensure normal document flow */
        z-index: 1; /* Low z-index to stay below other sections */
        margin-top: 40px; /* Add top margin to avoid crowding */
        margin-bottom: 40px; /* Add bottom margin for spacing */
    }

    .features-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 30px;
    }

    .feature-card {
        background: #f9fafb;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .feature-icon {
        margin-bottom: 20px;
        background: #e6f0fa;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .feature-icon svg {
        stroke: #264653;
        width: 32px;
        height: 32px;
        transition: stroke 0.3s ease;
    }

    .feature-card:hover .feature-icon svg {
        stroke: #1d3a47;
    }

    .feature-content h3 {
        font-size: 1.4rem;
        color: #1a1a1a;
        margin-bottom: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .feature-content p {
        font-size: 0.95rem;
        color: #555;
        line-height: 1.6;
        max-width: 90%;
        margin: 0 auto;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .features-container {
            grid-template-columns: 1fr;
        }

        .feature-card {
            padding: 20px;
        }

        .feature-content h3 {
            font-size: 1.2rem;
        }

        .feature-content p {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .features-section {
            padding: 40px 0;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .features-container {
            padding: 0 10px;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
        }

        .feature-icon svg {
            width: 28px;
            height: 28px;
        }
    }
</style>

<div class="features-section">
    <div class="features-container">
        <div class="feature-card">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="feature-content">
                <h3>Over 1300 drinks available</h3>
                <p>SilverAnchorE has a wide selection of wines, gins, vodkas, rums, tequila, liqueurs and other drinks to choose from.</p>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="feature-content">
                <h3>24/7 Express Delivery</h3>
                <p>We deliver any time, any day, in less than 1 hour - average: 23 min. We also offer next-day countrywide alcohol delivery in Kenya.</p>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </div>
            <div class="feature-content">
                <h3>Free pick up</h3>
                <p>At any of our store locations across Nairobi and Kenya.</p>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
            </div>
            <div class="feature-content">
                <h3>100% Secure Checkout</h3>
                <p>Pay online with M-Pesa Express, Online VISA (3D Secure), PayPal and BitPay. We also accept Cash and PDQ (card payment) on delivery.</p>
            </div>
        </div>
    </div>
</div>
