# Description

### Routes

The bidashboard project includes several routes that serve different purposes. Here's an overview of each route and what
it does:

- `/bidashboard/`:
    - Purpose: This is the main page route of the bidashboard. It serves as the landing page when users access the
      bidashboard application.
    - Description: When users navigate to the base URL `/bidashboard/`, they are directed to the main page of the
      bidashboard. This page may provide an overview of key metrics, general dashboard information, or a customizable
      dashboard layout with widgets and data visualizations. From this main page, users can access various other routes
      and functionalities of the bidashboard.

- `/bidashboard/report-widget`:
    - Purpose: This route displays widgets on the dashboard. Widgets are small, self-contained data visualizations or
      components that provide a quick overview of key metrics.


- `/bidashboard/report-page`:
    - Purpose: This route is responsible for displaying the widgets' information on the report pages of the bidashboard.
      Each page can be categorized as either daily or monthly, and the displayed information varies based on this
      categorization. Monthly pages show information for each of the 30 days of the month, while annual pages show the
      aggregated information for the entire year. Additionally, there are filters available above the page display that
      allow users to access information from different years or months. Users can also add widgets to the page, ensuring
      that the type of widget aligns with the page's categorization (daily or monthly).
    - Description: The Report Page of the bidashboard provides a customizable view where users can arrange widgets and
      visualize data specific to each page's categorization. Users can create monthly or annual pages, and based on the
      selected page type, the widgets will display data accordingly. The filters above the page display enable users to
      narrow down the view to a specific year or month, offering more granular control over the data displayed.
      Furthermore, users can add widgets of the appropriate type (daily or monthly) to the page, ensuring that the
      content is relevant and meaningful.
    - CRUD Operations:
        - Create: Users can create new report pages, specifying whether the page is daily or monthly.
        - Read: The route retrieves and displays existing report pages, showing the appropriate widgets and data based
          on the categorization (daily or monthly) and selected filters (year or month).
        - Update: Users can update the name of existing report pages.
        - Delete: Users can delete report pages that are no longer needed or relevant.

- `/bidashboard/report-year`:
    - Purpose: This route is responsible for handling the creation of years for use in the Select2 input field, which is
      located on the View Report Page of the bidashboard.
    - Description: On the View Report Page, there is a Select2 input field that allows users to choose specific years
      for their report. When users interact with this input field, the route `/bidashboard/report-year` is called to
      fetch the list of available years. The route retrieves the necessary data and returns it in a format suitable for
      populating the Select2 dropdown with the list of selectable years.

- `/bidashboard/sharing-page`:
    - Purpose: This route is responsible for managing the sharing settings and permissions for specific pages in the
      bidashboard. It allows users with appropriate privileges to control who is allowed to access the desired page and
      how long that access is valid.
    - Description: On the Sharing Page, users can specify access rights and permissions for individual pages within the
      bidashboard. These permissions determine which users or user groups have access to view or interact with specific
      pages. Additionally, users can set the validity period for the shared access, allowing them to control the
      duration during which shared access remains active.
- `/bidashboard/report-model-class`:
    - Purpose: Retrieve and manage available model widget classes for the bidashboard application.
    - Description: This path provides an organized list of model classes, showing their names and descriptions. Users can customize each model class with various options, preview how they will look with sample data, and create new samples. Existing widget instances can be edited. 
  Each item is created when creating a widget, if it does not already have a widget
- `/bidashboard/report-dashboard`:
    - Purpose: A dashboard to display the information of various widgets, both daily and monthly, and compare them with each other in the visual form of cards, charts and tables.
    

# Explain Different Part

### Widgets on the Invoices Page

In the example photo above, we can see the widgets applied to the "Invoices" page. There are two buttons that allow
users to add new widgets related to the "Invoices" model. The list of widgets showcases different metrics and data
visualizations relevant to invoice management, providing valuable insights for business analysis.

![btn create widget in view](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/btn-create-widget-in-view.png)

### Widget Creation Model

The Widget Creation Model offers users an intuitive and user-friendly interface to configure and customize widgets,
tailoring them to display the desired output fields and information. In the provided example image, you can observe the
Widget Creation Model, empowering users with the flexibility to select the output fields they wish to showcase. It is
essential to note that certain default output fields remain fixed and cannot be edited.

Using the Widget Creation Model, users can:

- Choose Output Fields: Users can effortlessly select and fine-tune the output fields they wish to exhibit within the
  widget. This model provides a seamless way to tailor the data presented, catering to specific business requirements.

- Leverage Default Output Fields: To expedite widget setup, the model offers default output field options that users can
  readily utilize. This feature simplifies the inclusion of common metrics and data points.

- Define Filters and Aggregations: Users can specify filters and aggregations to focus on specific data subsets and
  obtain
  summarizations, enhancing data exploration and insights.

![modal create widget](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/modal-create-widget.png)

### Page Types and Widget Actions

In the bidashboard, there are two types of pages: day and month.

### Day

![Page Day](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/page-daily.png)

### Month

![Page Month](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/page.png)

Each widget on the view page is accompanied by five buttons that provide various actions:

- Delete Widget: Allows users to remove the widget from the page if it's no longer needed or relevant.

- Edit Widget: Enables users to modify the widget's settings, such as output fields or visualization type.

- Run Widget: Allows users to manually run the widget to fetch and display the latest data.

- Go to Model: Directs users to the model associated with the widget, providing additional details and context.

- Display with Charts: Presents the data from the widget with interactive charts, providing visual insights.

By offering these buttons, the bidashboard empowers users to interact with and customize widgets to suit their specific
needs. Users can effortlessly manage their dashboard, visualize data, and gain valuable insights for business
intelligence.

### Displaying Widget Results with Different Chart Types

The Bidashboard extension allows you to visualize the results of a widget using different chart types, providing you
with versatile data representations. Currently, the extension supports the following chart types:

1. Area Chart
   ![Area Chart](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/chart-area.png)

2. Columns Chart
   ![Columns Chart](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/chart-column.png)

3. Pie Chart
   ![Pie Chart](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/chart-pie.png)




### Report Model Class

In this image, you can see an example of reportModelClass that was created when the widget was created, and the user registered a desired title for them.

![report model class](https://raw.githubusercontent.com/Sadi01/yii2-bi-dashboard/master/src/img/guide/report_model_class.png)