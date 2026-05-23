<div style="font-family: sans-serif; color: #111827;">
    <h2>New student request for your post</h2>
    <p>Hello {{ $post->user_id ? '' : '' }},</p>
    <p>{{ $student->name }} has sent a request for your post titled "{{ $post->title }}".</p>
    <p>Message:</p>
    <blockquote style="background:#f3f4f6;padding:12px;border-radius:8px">{{ $requestModel->description }}</blockquote>
    <p>View the student's profile on the platform to respond and confirm.</p>
    <p>Thanks,<br/>TutorLink BD</p>
</div>