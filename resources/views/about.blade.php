@extends('layouts.app')

@section('content')
    <div class="about-container">
        <div class="content-grid">
            <div class="image-container">
                <img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=800&h=1000&fit=crop" alt="Premium spirits collection">
            </div>
            <div class="text-content">
                <h2>A Legacy of Taste</h2>
                <p>Silveranchore Liqour store is your trusted online destination for <span class="highlight">premium Gin, Vodka, Beer, Spirits and fine Wines</span> in Rongai, Nairobi. Our passion for exceptional beverages runs deep, bringing you carefully curated selections worldwide.</p>
                <p>We believe every bottle tells a story—from the distilleries nestled in Scottish highlands to the sun-drenched vineyards of Tuscany. Our carefully curated selection represents journeys across the country and decades of craftsmanship.</p>
                <p>Founded in 2019, we aim to bring the best products to your doorstep, if you're in Nairobi. Here, you won't find just products on shelves—you'll discover a team genuinely excited to share knowledge and recommend the perfect pairing for your celebration.</p>
            </div>
        </div>

        <div class="stats-section">
            <div class="stat-item">
                <span class="stat-number">400+</span>
                <span class="stat-label">Unique Bottles and Flavour</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">5*</span>
                <span class="stat-label">Best Customer Service</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">10K+</span>
                <span class="stat-label">Happy Customers</span>
            </div>
        </div>

        <div class="content-grid reverse">
            <div class="text-content">
                <h2>Beyond Retail</h2>
                <p>We're more than a liquor store—we're your partners in celebration, contemplation, parties, and discovery. Whether you're a seasoned whiskey seeking that elusive vintage or someone taking their first steps into the world of craft spirits and wine, we're here to guide you.</p>
                <p>Our team continuously educates themselves to bring you not just products, but experiences. Every recommendation comes from genuine tasting experience. Every bottle in our collection has been chosen with intention and care.</p>
                <p>Our mission is to make shopping convenient and enjoyable for everyone, with exceptional customer service and fast, reliable delivery across Nairobi.</p>
            </div>
            <div class="image-container">
                <img src="https://images.unsplash.com/photo-1569529465841-dfecdab7503b?w=800&h=1000&fit=crop" alt="Craft cocktails and spirits">
            </div>
        </div>

        <div class="values-section">
            <div class="section-header">
                <h2>What We Stand For</h2>
                <div class="decorative-line"></div>
            </div>
            
            <div class="values-grid">
                <div class="value-card">
                    <h3>Authenticity</h3>
                    <p>Every bottle is sourced through verified channels. We guarantee authenticity and proper storage conditions for every spirit that passes through our doors.</p>
                </div>
                <div class="value-card">
                    <h3>Education</h3>
                    <p>We host monthly tastings, workshops, and pairing events. Knowledge shared is pleasure multiplied, and we love bringing people together around exceptional drinks.</p>
                </div>
                <div class="value-card">
                    <h3>Sustainability</h3>
                    <p>We prioritize producers who care for their land and communities. Our packaging is recyclable, and we support local distilleries pursuing eco-friendly practices.</p>
                </div>
                <div class="value-card">
                    <h3>Community</h3>
                    <p>Local partnerships matter. We collaborate with restaurants, support neighborhood events, and believe great spirits bring people together in meaningful ways.</p>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <h2>Get In Touch</h2>
            <p style="font-size: 18px; color: #666; margin-bottom: 40px;">Experience the difference that passion and expertise make</p>
            <a href="{{ route('contact') }}" class="cta-button">Contact Us</a>
        </div>
    </div>

<x-features-section />

@endsection