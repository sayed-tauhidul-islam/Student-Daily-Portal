<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Register · TutorLink BD</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet" />
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:     #0c2d48;
    --navy-mid: #134e75;
    --sky:      #0ea5e9;
    --sky-pale: #e0f2fe;
    --green:    #059669;
    --green-light: #d1fae5;
    --slate-50: #f8fafc;
    --slate-100:#f1f5f9;
    --slate-200:#e2e8f0;
    --slate-300:#cbd5e1;
    --slate-400:#94a3b8;
    --slate-500:#64748b;
    --slate-700:#334155;
    --slate-800:#1e293b;
    --white:    #ffffff;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --shadow-card: 0 4px 24px rgba(12,45,72,.10), 0 1px 4px rgba(12,45,72,.06);
    --transition: .18s cubic-bezier(.4,0,.2,1);
  }

  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--slate-100);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    position: relative;
    overflow-x: hidden;
  }

  /* subtle geometric bg pattern */
  body::before {
    content: '';
    position: fixed; inset: 0; z-index: 0;
    background-image:
      radial-gradient(circle at 20% 20%, rgba(14,165,233,.07) 0%, transparent 50%),
      radial-gradient(circle at 80% 75%, rgba(5,150,105,.07) 0%, transparent 50%);
    pointer-events: none;
  }

  .page-wrapper {
    position: relative; z-index: 1;
    width: 100%; max-width: 980px;
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-card);
    display: grid;
    grid-template-columns: 340px 1fr;
    overflow: hidden;
    animation: fadeUp .4s ease both;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ─── LEFT PANEL ─── */
  .left {
    background: linear-gradient(160deg, var(--navy) 0%, var(--navy-mid) 100%);
    padding: 2.5rem 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    position: relative;
    overflow: hidden;
  }

  .left::after {
    content: '';
    position: absolute; bottom: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    border: 40px solid rgba(255,255,255,.04);
    pointer-events: none;
  }

  .brand-row {
    display: flex; align-items: center; gap: 8px;
  }
  .brand-icon {
    width: 32px; height: 32px; border-radius: 8px;
    background: var(--sky);
    display: flex; align-items: center; justify-content: center;
  }
  .brand-icon svg { width: 18px; height: 18px; fill: white; }
  .brand-name { font-size: 13px; font-weight: 700; color: #bae6fd; letter-spacing: .03em; }

  .left-headline {
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 26px;
    line-height: 1.3;
    color: var(--white);
    margin-top: .25rem;
  }
  .left-headline em { font-style: italic; color: #7dd3fc; }

  .left-sub {
    font-size: 13px;
    color: #93c5fd;
    line-height: 1.65;
  }

  .feature-list {
    list-style: none;
    display: flex; flex-direction: column; gap: 12px;
    margin-top: .5rem;
  }
  .feature-list li {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 12.5px; color: #bae6fd; line-height: 1.5;
  }
  .check-icon {
    flex-shrink: 0;
    width: 18px; height: 18px;
    background: rgba(5,150,105,.3);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-top: 1px;
  }
  .check-icon svg { width: 10px; height: 10px; stroke: #34d399; fill: none; stroke-width: 2.5; }

  .stat-row {
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    margin-top: .5rem;
  }
  .stat-card {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.10);
    border-radius: var(--radius-sm);
    padding: 12px;
  }
  .stat-num { font-size: 20px; font-weight: 700; color: var(--white); }
  .stat-label { font-size: 10.5px; color: #7dd3fc; margin-top: 2px; }

  .left-footer {
    margin-top: auto;
    font-size: 12px; color: #7dd3fc;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,.08);
  }
  .left-footer a { color: var(--white); font-weight: 600; text-decoration: none; }
  .left-footer a:hover { text-decoration: underline; }

  /* ─── RIGHT PANEL ─── */
  .right {
    padding: 2.25rem 2.25rem 2rem;
    display: flex; flex-direction: column; gap: 1.25rem;
    overflow-y: auto;
  }

  .form-head h2 {
    font-size: 20px; font-weight: 700; color: var(--slate-800);
  }
  .form-head p { font-size: 13px; color: var(--slate-500); margin-top: 3px; }

  /* Role tabs */
  .role-tabs {
    display: flex; gap: 6px;
    background: var(--slate-100);
    padding: 4px;
    border-radius: var(--radius-md);
  }
  .role-btn {
    flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 6px;
    border: none; border-radius: 9px;
    font-family: inherit; font-size: 12.5px; font-weight: 500;
    cursor: pointer;
    background: transparent; color: var(--slate-500);
    transition: background var(--transition), color var(--transition), box-shadow var(--transition);
  }
  .role-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; flex-shrink: 0; }
  .role-btn.active {
    background: var(--white);
    color: var(--navy);
    box-shadow: 0 1px 6px rgba(12,45,72,.12);
    font-weight: 600;
  }

  /* Section divider */
  .section-divider {
    display: flex; align-items: center; gap: 8px;
  }
  .section-divider::before, .section-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--slate-200);
  }
  .section-divider span {
    font-size: 10.5px; font-weight: 600; color: var(--slate-400);
    text-transform: uppercase; letter-spacing: .07em; white-space: nowrap;
  }

  /* Field grid */
  .field-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  }
  .field-grid.cols-1 { grid-template-columns: 1fr; }

  .field { display: flex; flex-direction: column; gap: 4px; }
  .field label {
    font-size: 11.5px; font-weight: 600; color: var(--slate-600, #475569);
    letter-spacing: .02em;
  }
  .field-wrap { position: relative; }
  .field-wrap .icon {
    position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
    color: var(--slate-400); pointer-events: none;
    display: flex; align-items: center;
  }
  .field-wrap .icon svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; }
  .field-wrap input,
  .field-wrap select,
  .field-wrap textarea {
    width: 100%; padding: 9px 10px 9px 32px;
    border: 1.5px solid var(--slate-200);
    border-radius: var(--radius-sm);
    font-family: inherit; font-size: 13px; color: var(--slate-800);
    background: var(--white); outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
  }
  .field-wrap.no-icon input,
  .field-wrap.no-icon select,
  .field-wrap.no-icon textarea { padding-left: 12px; }
  .field-wrap input:focus,
  .field-wrap select:focus,
  .field-wrap textarea:focus {
    border-color: var(--sky);
    box-shadow: 0 0 0 3px rgba(14,165,233,.12);
  }
  .field-wrap input::placeholder,
  .field-wrap textarea::placeholder { color: var(--slate-300); }
  .field-wrap select { cursor: pointer; appearance: none; }
  .field-wrap .select-caret {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    pointer-events: none; color: var(--slate-400);
  }
  .field-wrap .select-caret svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }
  .field-wrap textarea { resize: none; height: 80px; padding-left: 12px; }
  .pw-toggle {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: var(--slate-400);
    display: flex; align-items: center; padding: 0;
    transition: color var(--transition);
  }
  .pw-toggle:hover { color: var(--slate-700); }
  .pw-toggle svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; }

  /* Role-specific sections */
  .role-section { display: none; }
  .role-section.show { display: block; }

  /* Submit */
  .submit-btn {
    width: 100%; padding: 12px;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
    color: var(--white);
    border: none; border-radius: var(--radius-sm);
    font-family: inherit; font-size: 14px; font-weight: 600;
    cursor: pointer; letter-spacing: .01em;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: opacity var(--transition), transform var(--transition);
  }
  .submit-btn:hover { opacity: .92; }
  .submit-btn:active { transform: scale(.99); }
  .submit-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; }

  .terms-text {
    font-size: 11px; color: var(--slate-400); text-align: center;
  }
  .terms-text a { color: var(--slate-500); text-decoration: underline; }

  /* Live preview */
  .preview-card {
    background: var(--slate-50);
    border: 1.5px solid var(--slate-200);
    border-radius: var(--radius-sm);
    padding: 10px 12px;
    display: flex; align-items: center; gap: 10px;
  }
  .preview-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
    color: var(--white); font-size: 12px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; letter-spacing: .02em;
  }
  .preview-text p { font-size: 13px; font-weight: 600; color: var(--slate-800); }
  .preview-text span { font-size: 11.5px; color: var(--slate-500); }
  .preview-label {
    font-size: 10px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .08em; color: var(--slate-400); margin-left: auto; align-self: center;
    background: var(--slate-200); padding: 3px 8px; border-radius: 20px;
  }

  /* ─── RESPONSIVE ─── */
  @media (max-width: 720px) {
    .page-wrapper { grid-template-columns: 1fr; }
    .left { display: none; }
    .right { padding: 1.75rem 1.25rem; }
    .field-grid { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

<main class="page-wrapper" aria-label="Create account">

  <!-- LEFT -->
  <aside class="left" aria-label="TutorLink BD benefits">
    <div class="brand-row">
      <div class="brand-icon">
        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 2L3 7v6l7 5 7-5V7L10 2z"/></svg>
      </div>
      <span class="brand-name">TutorLink BD</span>
    </div>

    <div>
      <h1 class="left-headline">Smart, fast onboarding for <em>students</em> and teachers</h1>
      <p class="left-sub">Create your account, complete a short profile, and get started with tutoring tools and classroom workflows.</p>
    </div>

    <ul class="feature-list" aria-label="Benefits">
      <li>
        <span class="check-icon"><svg viewBox="0 0 12 12" aria-hidden="true"><polyline points="2,6 5,9 10,3"/></svg></span>
        Fast account creation and immediate access
      </li>
      <li>
        <span class="check-icon"><svg viewBox="0 0 12 12" aria-hidden="true"><polyline points="2,6 5,9 10,3"/></svg></span>
        Role-based onboarding — student, teacher, or admin
      </li>
      <li>
        <span class="check-icon"><svg viewBox="0 0 12 12" aria-hidden="true"><polyline points="2,6 5,9 10,3"/></svg></span>
        Responsive, accessible, and professional UI
      </li>
      <li>
        <span class="check-icon"><svg viewBox="0 0 12 12" aria-hidden="true"><polyline points="2,6 5,9 10,3"/></svg></span>
        Connect with tutors and students across Bangladesh
      </li>
    </ul>

    <div class="stat-row" aria-label="Platform statistics">
      <div class="stat-card">
        <div class="stat-num">12K+</div>
        <div class="stat-label">Active students</div>
      </div>
      <div class="stat-card">
        <div class="stat-num">3.4K+</div>
        <div class="stat-label">Qualified tutors</div>
      </div>
    </div>

    <div class="left-footer">
      Already have an account? <a href="{{ route('login') }}" class="login-link">Sign in</a>
    </div>
  </aside>

  <!-- RIGHT -->
  <section class="right" aria-label="Registration form">

    <div class="form-head">
      <h2>Create your account</h2>
      <p>Choose a role and provide your basic information to get started.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate>
      @csrf
      <input type="hidden" name="role" id="roleInput" value="student" />
      @if ($errors->any())
        <div style="margin-bottom:1rem;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;padding:.75rem 1rem;border-radius:10px;font-size:12.5px;">
          {{ $errors->first() }}
        </div>
      @endif

      <!-- Role selector -->
      <div class="role-tabs" role="tablist" aria-label="Select account role" style="margin-bottom:1.25rem">
        <button type="button" class="role-btn active" data-role="student" role="tab" aria-selected="true" onclick="switchRole('student')">Student Panel</button>
        <button type="button" class="role-btn" data-role="teacher" role="tab" aria-selected="false" onclick="switchRole('teacher')">Teacher Panel</button>
        <button type="button" class="role-btn" data-role="teacher_admin" role="tab" aria-selected="false" onclick="switchRole('teacher_admin')">Teacher Admin Panel</button>
      </div>

      <!-- Basic info -->
      <div class="section-divider"><span>Basic information</span></div>
      <div class="field-grid" style="margin-top:1rem">
        <div class="field">
          <label for="name">Full name</label>
          <div class="field-wrap">
            <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>
            <input type="text" id="name" name="name" placeholder="e.g. Rafi Ahmed" autocomplete="name" oninput="updatePreview()" />
          </div>
        </div>
        <div class="field">
          <label for="email">Email address</label>
          <div class="field-wrap">
            <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg></span>
            <input type="email" id="email" name="email" placeholder="you@example.com" autocomplete="email" />
          </div>
        </div>
        <div class="field">
          <label for="phone">Phone <span style="color:var(--slate-400);font-weight:400">(optional)</span></label>
          <div class="field-wrap">
            <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.7A2 2 0 012 1h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg></span>
            <input type="tel" id="phone" name="phone" placeholder="+880 1X00-000000" autocomplete="tel" />
          </div>
        </div>
        <div class="field">
          <label for="area">City / Area</label>
          <div class="field-wrap">
            <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-7.27-7-11a7 7 0 0114 0c0 3.73-7 11-7 11z"/><circle cx="12" cy="10" r="2"/></svg></span>
            <input type="text" id="area" name="area" placeholder="Dhaka, Mirpur" oninput="updatePreview()" />
          </div>
        </div>
      </div>

      <!-- Password -->
      <div class="section-divider" style="margin-top:1.25rem"><span>Security</span></div>
      <div class="field-grid" style="margin-top:1rem">
        <div class="field">
          <label for="password">Password</label>
          <div class="field-wrap">
            <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg></span>
            <input type="password" id="password" name="password" placeholder="Min. 8 characters" autocomplete="new-password" />
            <button type="button" class="pw-toggle" onclick="togglePw('password','eyeA')" aria-label="Show password">
              <svg id="eyeA" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
        <div class="field">
          <label for="password_confirmation">Confirm password</label>
          <div class="field-wrap">
            <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg></span>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" autocomplete="new-password" />
            <button type="button" class="pw-toggle" onclick="togglePw('password_confirmation','eyeB')" aria-label="Show password">
              <svg id="eyeB" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Role-specific fields -->
      <div id="roleFields" style="margin-top:1.25rem">

        <!-- Student -->
        <div class="role-section show" id="sec-student">
          <div class="section-divider"><span>Student details</span></div>
          <div class="field-grid" style="margin-top:1rem">
            <div class="field">
              <label for="school_s">School / Institution</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                <input type="text" id="school_s" name="school" placeholder="e.g. Viqarunnisa Noon School" />
              </div>
            </div>
            <div class="field">
              <label for="class">Class / Grade</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg></span>
                <select id="class" name="class" onchange="handleStudentClassChange()">
                  <option value="">Select class</option>
                  <option value="1">Class 1</option>
                  <option value="2">Class 2</option>
                  <option value="3">Class 3</option>
                  <option value="4">Class 4</option>
                  <option value="5">Class 5</option>
                  <option value="6">Class 6</option>
                  <option value="7">Class 7</option>
                  <option value="8">Class 8</option>
                  <option value="9">Class 9</option>
                  <option value="10">Class 10</option>
                  <option value="11">Class 11</option>
                  <option value="12">Class 12</option>
                </select>
                <span class="select-caret"><svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg></span>
              </div>
            </div>
            <div class="field" id="student-group-field">
              <label for="group">Group</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></span>
                <select id="group" name="group">
                  <option value="">Select group</option>
                  <option value="Science">Science</option>
                  <option value="Business">Business</option>
                  <option value="Arts">Arts</option>
                </select>
                <span class="select-caret"><svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg></span>
              </div>
            </div>
            <div class="field">
              <label for="subjects_text">Subjects needed</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></span>
                <input type="text" id="subjects_text" name="subjects_text" placeholder="Math, Physics, English" />
              </div>
            </div>
          </div>
        </div>

        <!-- Teacher -->
        <div class="role-section" id="sec-teacher">
          <div class="section-divider"><span>Teacher details</span></div>
          <div class="field-grid" style="margin-top:1rem">
            <div class="field">
              <label for="qualification">Qualification</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>
                <input type="text" id="qualification" name="qualification" placeholder="e.g. M.Sc. Mathematics" />
              </div>
            </div>
            <div class="field">
              <label for="subject">Primary subject</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg></span>
                <input type="text" id="subject" name="subject" placeholder="e.g. Physics" />
              </div>
            </div>
            <div class="field">
              <label for="experience">Teaching experience</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                <input type="text" id="experience" name="experience" placeholder="e.g. 5 years" />
              </div>
            </div>
            <div class="field">
              <label for="school_t">Current school <span style="color:var(--slate-400);font-weight:400">(optional)</span></label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                <input type="text" id="school_t" name="school" placeholder="e.g. BRAC University" />
              </div>
            </div>
          </div>
        </div>

        <!-- Admin -->
        <div class="role-section" id="sec-teacher-admin">
          <div class="section-divider"><span>Organization info</span></div>
          <div class="field-grid cols-1" style="margin-top:1rem">
            <div class="field">
              <label for="school_a">School / College name</label>
              <div class="field-wrap">
                <span class="icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                <input type="text" id="school_a" name="school" placeholder="e.g. Dhaka College" />
              </div>
            </div>
            <div class="field">
              <label for="bio">About your school / organization</label>
              <div class="field-wrap no-icon">
                <textarea id="bio" name="bio" placeholder="Describe your institution, its mission, and how you plan to use TutorLink BD..."></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div style="margin-top:1.5rem;display:flex;flex-direction:column;gap:.75rem">
        <button type="submit" class="submit-btn">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
          Create account
        </button>

        <p class="terms-text">By creating an account you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>

        <!-- Live preview -->
        <div class="preview-card" aria-label="Profile preview">
          <div class="preview-avatar" id="prevAvatar">?</div>
          <div class="preview-text">
            <p id="prevName">Your name</p>
            <span id="prevMeta">Student · —</span>
          </div>
          <span class="preview-label" id="prevRole">Student</span>
        </div>
      </div>

    </form>
  </section>
</main>

<script>
(function(){
  let currentRole = 'student';

  function switchRole(r){
    currentRole = r;
    document.getElementById('roleInput').value = r;

    document.querySelectorAll('.role-btn').forEach(b=>{
      const active = b.dataset.role === r;
      b.classList.toggle('active', active);
      b.setAttribute('aria-selected', active);
    });

    document.querySelectorAll('.role-section').forEach(s=>s.classList.remove('show'));
    const sec = document.getElementById('sec-'+r);
    if(sec) sec.classList.add('show');
    syncRoleFieldState();
    handleStudentClassChange();

    updatePreview();
  }

  function syncRoleFieldState(){
    const sections = document.querySelectorAll('.role-section');
    sections.forEach(section => {
      const isActive = section.classList.contains('show');
      section.querySelectorAll('input, select, textarea').forEach(el => {
        if (el.id === 'name' || el.id === 'email' || el.id === 'phone' || el.id === 'area' || el.id === 'password' || el.id === 'password_confirmation') {
          return;
        }
        el.disabled = !isActive;
      });
    });
  }

  function handleStudentClassChange(){
    const classInput = document.getElementById('class');
    const groupField = document.getElementById('student-group-field');
    const groupInput = document.getElementById('group');
    if (!classInput || !groupField || !groupInput) return;

    const classValue = parseInt(classInput.value || '', 10);
    const shouldShowGroup = Number.isInteger(classValue) && classValue >= 9 && classValue <= 12;

    if (currentRole !== 'student') {
      groupField.style.display = 'none';
      groupInput.disabled = true;
      groupInput.required = false;
      groupInput.value = '';
      return;
    }

    groupField.style.display = shouldShowGroup ? '' : 'none';
    groupInput.disabled = !shouldShowGroup;
    groupInput.required = shouldShowGroup;
    if (!shouldShowGroup) {
      groupInput.value = '';
    }
  }

  function updatePreview(){
    const nm = (document.getElementById('name').value||'').trim();
    const ar = (document.getElementById('area').value||'').trim();
    const roleCap = currentRole.split(/[-_]/).map(w=> w.charAt(0).toUpperCase()+w.slice(1)).join(' ');
    const initials = nm ? nm.split(' ').map(w=>w[0]).join('').substring(0,2).toUpperCase() : '?';

    document.getElementById('prevAvatar').textContent = initials;
    document.getElementById('prevName').textContent = nm || 'Your name';
    document.getElementById('prevMeta').textContent = roleCap + ' · ' + (ar||'—');
    document.getElementById('prevRole').textContent = roleCap;
  }

  function togglePw(inputId, iconId){
    const input = document.getElementById(inputId);
    const closed = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    const slash = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
    if(input.type==='password'){ input.type='text'; document.getElementById(iconId).innerHTML=slash; }
    else { input.type='password'; document.getElementById(iconId).innerHTML=closed; }
  }

  window.switchRole = switchRole;
  window.updatePreview = updatePreview;
  window.togglePw = togglePw;
  window.handleStudentClassChange = handleStudentClassChange;

  switchRole('student');
  handleStudentClassChange();
})();
</script>
</body>
</html>
