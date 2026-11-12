<x-mail::message>
# Password Reset Request

Hello {{ $user->name }},

We received a request to reset the password for your **BPI-IPMS** account ({{ $user->email }}).

<x-mail::button :url="$resetUrl">
Reset Password
</x-mail::button>

<x-mail::panel>
**Security Notice**

- This password reset link will expire in **{{ $expireMinutes }} minutes**.
- If you did not request a password reset, you can safely ignore this email and your password will remain unchanged.
</x-mail::panel>

Thank you,<br>
BPI Planning and Management Office (PMO)
</x-mail::message>
