<x-mail::message>
# Account Request Submitted

Hello {{ $user->name }},

We have received your request for access to the **Bureau of Plant Industry - Integrated Performance Monitoring System (BPI-IPMS)**. Your account is currently **pending verification and approval** by the Planning and Management Office (PMO) Administrator.

<x-mail::panel>
**Request Summary**

- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Requested Role:** {{ $roleLabel }}
- **Operating Unit:** {{ $unitLabel }}
- **Status:** Pending PMO Review
</x-mail::panel>

You will receive another email once the PMO Administrator approves and activates your account.

<x-mail::button :url="$landingUrl">
Return to BPI-IPMS Portal
</x-mail::button>

Thank you,<br>
BPI Planning and Management Office (PMO)
</x-mail::message>
