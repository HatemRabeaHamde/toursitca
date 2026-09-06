<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background:#F5F2EF;font-family:'DM Sans',Helvetica,Arial,sans-serif;color:#18140F;}
  .wrap{max-width:560px;margin:32px auto;background:#FFFFFF;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.07);}
  .header{background:#18140F;padding:28px 36px;text-align:center;}
  .logo{font-size:22px;font-weight:700;color:#FFFFFF;letter-spacing:-.3px;}
  .logo em{font-style:italic;color:#B08D5C;}
  .body{padding:36px 36px 28px;}
  .title{font-size:20px;font-weight:700;color:#18140F;margin:0 0 10px;}
  .lead{font-size:15px;color:#3D3530;line-height:1.65;margin:0 0 24px;}
  .box{background:#FAF8F6;border:1.5px solid #EDE9E4;border-radius:12px;padding:20px 22px;margin-bottom:24px;}
  .box-row{display:flex;justify-content:space-between;align-items:baseline;padding:6px 0;border-bottom:1px solid #EDE9E4;font-size:13.5px;}
  .box-row:last-child{border-bottom:none;}
  .box-label{color:#8B7B72;font-weight:500;}
  .box-value{color:#18140F;font-weight:600;text-align:right;}
  .btn{display:inline-block;background:#8B5E48;color:#FFFFFF!important;text-decoration:none;padding:13px 28px;border-radius:10px;font-size:14px;font-weight:600;letter-spacing:.01em;margin:4px 0 20px;}
  .divider{border:none;border-top:1px solid #EDE9E4;margin:24px 0;}
  .footer{background:#F5F2EF;padding:20px 36px;text-align:center;}
  .footer p{font-size:12px;color:#A89F97;margin:4px 0;line-height:1.5;}
  .footer a{color:#8B5E48;text-decoration:none;}
  .badge{display:inline-block;background:#EDE9E4;color:#6B6259;font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;padding:3px 10px;border-radius:20px;margin-bottom:16px;}
  .badge--success{background:#D1FAE5;color:#065F46;}
  .badge--warning{background:#FEF3C7;color:#92400E;}
  .badge--error{background:#FEE2E2;color:#991B1B;}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">{{ config('brand.name_first') }}<em>{{ config('brand.name_second') }}.</em></div>
  </div>
  <div class="body">
    @yield('content')
  </div>
  <div class="footer">
    <p>{{ config('app.name') }} · Morocco Tourism Platform</p>
    <p>Questions? <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
    <p style="margin-top:10px;font-size:11px;color:#C4B9B0;">You received this email because you made a booking or registration on {{ config('app.name') }}.</p>
  </div>
</div>
</body>
</html>
