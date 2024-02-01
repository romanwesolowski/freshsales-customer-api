# WebIt_FreshSales module documentation

### Installation

put files of the module into /app/code directory

next run commands:
- bin/magento s:up
- bin/magento c:f
- bin/magento s:d:c

### Overview

module allow us to save customer into FreshSales account when have 
created magento account via frontend

required domain name and app token is configurable in 
Stores > Configuration > WebIt > FreshSales

if the FreshSales account is created successfully additional attribute 
is saved to customer and it's visible into Admin Panel 
under Personal Info tab in Customer's edit section

if creating of the FreshSales account will failed, 
error will occured into var/log/WebIt_FreshSales/custom.log file

