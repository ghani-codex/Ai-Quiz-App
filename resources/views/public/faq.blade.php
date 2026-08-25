@extends('layouts.public')

@section('title', 'Frequently Asked Questions')

@section('content')
    <section id="faq" class="faq section">
        <div class="container section-title" data-aos="fade-up">
            <h2>FAQ</h2>
            <p>Everything you might want to ask</p>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="faq-container">
                        @php
                            $left = [
                                ['How does the Agent generate quizzes?', 'When a teacher uploads a lecture, the platform extracts the text and sends it to the Agent with a strict JSON schema. The Agent returns N multiple-choice questions, each grounded in the source. Teachers can edit or regenerate before publishing.'],
                                ['Is it really free?', 'Yes — students enrol for free. The Agent runs on free-tier APIs (Gemini primary, Groq fallback), so cost stays near zero. Paid tiers exist for institutions wanting bulk teacher onboarding and priority queue.'],
                                ['What file formats are supported?', 'PDF and DOCX are the primary upload formats. You can also paste raw text directly into the lecture editor.'],
                                ['How long does generation take?', 'Around 5–15 seconds for a typical 10-question MCQ quiz from a 5-page lecture, depending on Agent provider load.'],
                            ];
                            $right = [
                                ['How are teachers verified?', 'Every teacher signup is reviewed and approved by an admin from the Filament dashboard. Until approved, teacher accounts cannot publish courses or generate quizzes.'],
                                ['What if the Agent is wrong?', 'Teachers always review generated questions before publishing. They can edit, regenerate, or delete any question.'],
                                ['What is personalised feedback?', "After every quiz submission, the LLM is given your wrong answers + the correct ones and produces a short paragraph that names your weakest topics and suggests what to review next."],
                                ['Can I use it on mobile?', 'Yes — the entire platform is mobile-responsive. Browsing courses, taking quizzes, and even the teacher / admin dashboards all work on a phone.'],
                            ];
                        @endphp
                        @foreach($left as $i => [$q, $a])
                            <div class="faq-item {{ $i === 0 ? 'faq-active' : '' }}">
                                <h3>{{ $q }}</h3>
                                <div class="faq-content"><p>{{ $a }}</p></div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="faq-container">
                        @foreach($right as $q)
                            <div class="faq-item">
                                <h3>{{ $q[0] }}</h3>
                                <div class="faq-content"><p>{{ $q[1] }}</p></div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
