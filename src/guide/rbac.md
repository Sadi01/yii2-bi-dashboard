# RBAC (Role-Based Access Control) Configuration

## Introduction

RBAC (Role-Based Access Control) is a method of managing access to resources in a system based on the roles assigned to
users. In the context of Yii 2, RBAC is implemented using the AccessControl class to define access rules for different
user roles to specific actions in a controller.
Now we introduce the access to the 6 controllers we have:

## permissions  for ReportPageController

### Action: index
permission: ['BI/ReportPage/index']
### Action: view
permission: ['BI/ReportPage/view']
### Action: create
permission: ['BI/ReportPage/create']
### Action: update
permission: ['BI/ReportPage/update']
### Action: delete
permission: ['BI/ReportPage/delete']
### Action: update-widget
permission: ['BI/ReportPage/update-widget']
### Action: add
permission: ['BI/ReportPage/add']
### Action: get-widget-column
permission: ['BI/ReportPage/get-widget-column']
### Action: run-all-widgets
permission: ['BI/ReportPage/run-all-widgets']


## permissions for ReportPageWidgetController
### Action: delete
permission: ['BI/ReportWidget/delete']


## permissions for DefaultController
### Action: index
permission: ['BI/Default/index']


## permissions for ReportWidgetController
### Action: index
permission: ['BI/ReportWidget/index']
### Action: view
permission: ['BI/ReportWidget/view']
### Action: create
permission: ['BI/ReportWidget/create']
### Action: update
permission: ['BI/ReportWidget/update']
### Action: delete
permission: ['BI/ReportWidget/delete']
### Action: open-modal
permission: ['BI/ReportWidget/open-modal']
### Action: run
permission: ['BI/ReportWidget/run']
### Action: modal-show-chart
permission: ['BI/ReportWidget/modal-show-chart']



## permissions for ReportYearController
### Action: index
permission: ['BI/ReportYear/index']
### Action: view
permission: ['BI/ReportYear/view']
### Action: create
permission: ['BI/ReportYear/create']
### Action: update
permission: ['BI/ReportYear/update']
### Action: delete
permission: ['BI/ReportYear/delete']



## permissions for SharingPageController
## Action: index
permission: ['BI/SharingPage/index']
### Action: view
permission: ['BI/SharingPage/view']
### Action: create
permission: ['BI/SharingPage/create']
### Action: update
permission: ['BI/SharingPage/update']
### Action: delete
permission: ['BI/SharingPage/delete']
### Action: managment
permission: ['BI/SharingPage/managment']
### Action: expire
permission: ['BI/SharingPage/expire']

## permissions for ReportModelClass
## Action: index
permission: ['BI/ReportModelClass/index']
### Action: view
permission: ['BI/ReportModelClass/view']

## Permissions for ReportDashboardController
## Action:index
permission: ['BI/ReportDashboard/index']
## Action:view
permission: ['BI/ReportDashboard/view']
## Action:create
permission: ['BI/ReportDashboard/create']
## Action:update
permission: ['BI/ReportDashboard/update']
## Action:delete
permission: ['BI/ReportDashboard/delete']

## Permissions for ReportBoxController
## Action:create
permission: ['BI/ReportBox/create']
## Action:update
permission: ['BI/ReportBox/update']
## Action:delete
permission: ['BI/ReportBox/delete']

## Permissions for ReportBoxWidgetController
## Action:create
permission: ['BI/ReportBoxWidget/create']
## Action:update
permission: ['BI/ReportBoxWidget/update']
## Action:delete
permission: ['BI/ReportBoxWidget/delete']


This project document provides an overview of the Role-Based Access Control (RBAC) configuration for the controllers and
their respective actions. The defined access rules ensure that only authorized users with specific roles can access and
perform actions within the system. If you have any further questions or need assistance, please feel free to reach out
to the project team.

