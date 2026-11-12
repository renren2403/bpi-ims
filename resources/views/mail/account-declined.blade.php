<x-mail::message>
# Account Request Declined

Hello {{ $user->name }},

Your access request for the **Bureau of Plant Industry - Integrated Performance Monitoring System (BPI-IPMS)** was **declined** following PMO verification.

<x-mail::panel>
**Request Details**

- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Requested Role:** {{ $roleLabel }}
- **Operating Unit:** {{ $unitLabel }}
- **Status:** Declined
@if ($user->approval_remarks)
- **Remarks:** {{ $user->approval_remarks }}
@endif
</x-mail::panel>

If you believe this was declined in error or need assistance updating your unit assignment, please contact the Planning and Management Office at **pmo@bpi.gov.ph**.

<x-mail::button :url="$portalUrl">
Visit BPI-IPMS Portal
</x-mail::button>

Thank you,<br>
BPI Planning and Management Office (PMO)
</x-mail::message>
