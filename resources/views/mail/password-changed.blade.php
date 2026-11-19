<x-mail::message>
# Password Changed Successfully

Hello {{ $user->name }},

This is a security confirmation that the password for your **Bureau of Plant Industry - Integrated Performance Monitoring System (BPI-IPMS)** account ({{ $user->email }}) was recently changed.

<x-mail::panel>
**Security Details**

- **Account:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Date & Time:** {{ $changedAt }}
- **IP Address:** {{ $ipAddress }}
</x-mail::panel>

If you made this change, you can safely ignore this email.

**Did not make this change?**
If you did not authorize this change, please reset your password immediately using the link below or contact the Planning and Management Office (PMO) Administrator.

<x-mail::button :url="$resetUrl">
Secure / Reset Password
</x-mail::button>

Thank you,<br>
BPI Planning and Management Office (PMO)
</x-mail::message>
