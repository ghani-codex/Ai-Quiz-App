<footer id="footer" class="footer">
    <div class="footer-newsletter">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-6">
                    <h4>Stay up to date</h4>
                    <p>Get product updates, Agent-learning tips and new course alerts in your inbox.</p>
                    <form action="{{ route('newsletter.store') }}" method="post" class="php-email-form">
                        @csrf
                        <div class="newsletter-form">
                            <input type="email" name="email" required>
                            <input type="submit" value="Subscribe">
                        </div>
                        @if (session('status'))
                            <div class="sent-message d-block">{{ session('status') }}</div>
                        @endif
                        @error('email')
                            <div class="error-message d-block">{{ $message }}</div>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6 footer-about">
                <a href="{{ route('home') }}" class="d-flex align-items-center">
                    <span class="sitename">AI Quiz</span>
                </a>
                <div class="footer-contact pt-3">
                    <p>The Islamia University of Bahawalpur</p>
                    <p>Rahim Yar Khan Campus, Pakistan</p>
                    <p class="mt-3"><strong>Email:</strong> <span>info@aiquiz.app</span></p>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{ route('home') }}">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{ route('courses.index') }}">Browse Courses</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{ route('pricing') }}">Pricing</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{ route('faq') }}">FAQ</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>For Educators</h4>
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{ route('register') }}">Become a Teacher</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{ route('home') }}#features">Features</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{ route('contact') }}">Contact Sales</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12">
                <h4>Built for the FYP</h4>
                <p>AI-Powered E-Learning Platform with Smart Quiz Generator — a final-year project at IUB, Rahim Yar Khan Campus.</p>
                <div class="social-links d-flex">
                    <a href="#"><i class="bi bi-github"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="container copyright text-center mt-4">
        <p>&copy; <span>{{ date('Y') }}</span> <strong class="px-1 sitename">AI Quiz</strong> <span>All Rights Reserved</span></p>
        <div class="credits">
            Built with Laravel + Filament. Template by <a href="https://bootstrapmade.com/">BootstrapMade</a>.
        </div>
    </div>
</footer>
