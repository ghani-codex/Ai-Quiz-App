@extends('layouts.public')

@section('title', 'Contact Us')

@section('content')
    <section id="contact" class="contact section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Contact</h2>
            <p>Send us a message</p>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="200">
                                <i class="bi bi-geo-alt"></i>
                                <h3>Address</h3>
                                <p>The Islamia University of Bahawalpur</p>
                                <p>Rahim Yar Khan Campus, Pakistan</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="300">
                                <i class="bi bi-envelope"></i>
                                <h3>Email Us</h3>
                                <p>info@aiquiz.app</p>
                                <p>support@aiquiz.app</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="400">
                                <i class="bi bi-clock"></i>
                                <h3>Open Hours</h3>
                                <p>Monday - Saturday</p>
                                <p>9:00AM - 6:00PM PKT</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="500">
                                <i class="bi bi-people"></i>
                                <h3>Project team</h3>
                                <p>Haseeb Ur Rehman</p>
                                <p>Hassnain Mushtaq</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <form action="{{ route('contact.store') }}" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                        @csrf
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" value="{{ old('name') }}" required>
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" placeholder="Your Email" value="{{ old('email') }}" required>
                                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" name="subject" placeholder="Subject" value="{{ old('subject') }}" required>
                                @error('subject')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" name="message" rows="6" placeholder="Message" required>{{ old('message') }}</textarea>
                                @error('message')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-12 text-center">
                                @if (session('status'))
                                    <div class="sent-message d-block">{{ session('status') }}</div>
                                @endif
                                <button type="submit" class="btn-buy">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
