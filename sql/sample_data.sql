-- Sample data for GeoPortfolio Pro
USE geoportfolio_pro;

-- Insert categories
INSERT INTO categories (name, type) VALUES
('GIS', 'work'),
('Remote Sensing', 'work'),
('Research', 'work'),
('Experimental', 'work'),
('Web Development', 'work'),
('Technical Analysis', 'blog'),
('Tutorials', 'blog'),
('Case Studies', 'blog');

-- Insert default admin user (password: admin)
INSERT INTO users (name, email, password, role, status) VALUES
('Jane Doe', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'active'),
('John Smith', 'editor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor', 'active');

-- Insert sample works
INSERT INTO works (title, description, long_description, image_url, category, tags, link, image_style, place) VALUES
('Urban Heat Island Analysis', 'Leveraging Landsat 8 data to map UHI effects in major metropolitan areas.', 
'<h2>Project Overview</h2><p>This project utilized thermal bands from Landsat 8 satellite imagery to conduct a comprehensive analysis of the Urban Heat Island (UHI) effect across several major cities. The study involved data preprocessing, land surface temperature calculation, and spatial analysis to identify hotspots and correlate them with urban land use patterns.</p>', 
'https://picsum.photos/seed/gis1/600/400', 'GIS', '["GIS", "Thermal Analysis", "Urban Planning"]', '#/works', 'cover', 'Global Cities'),

('Deforestation Monitoring in the Amazon', 'Using Sentinel-2 imagery and time-series analysis to track forest loss.',
'A multi-year study using Sentinel-2 satellite data to monitor and quantify deforestation rates in the Amazon rainforest. Change detection algorithms were applied to time-series imagery to create an automated monitoring system, providing near real-time alerts on deforestation activities.',
'https://picsum.photos/seed/rs1/600/400', 'Remote Sensing', '["Remote Sensing", "Environment", "Change Detection"]', '#/works', 'cover', NULL),

('Crop Yield Prediction Model', 'A research paper on using machine learning with satellite data to forecast crop yields.',
'This research focused on developing a machine learning model to predict corn yield based on multispectral satellite imagery and weather data. Various vegetation indices (e.g., NDVI) were extracted and used as features for a Random Forest regression model, achieving high accuracy in yield prediction.',
'https://picsum.photos/seed/research1/600/400', 'Research', '["Machine Learning", "Agriculture", "Data Science"]', '#/works', 'cover', 'Midwest, USA');

-- Insert sample blog posts
INSERT INTO blog_posts (title, summary, content, image_url, publish_date, author, category, image_style, place) VALUES
('The Future of GIS: Integrating AI and Big Data', 'Exploring how artificial intelligence and big data are revolutionizing the field of geospatial analysis, from automated feature extraction to predictive modeling.',
'<h2>The Next Evolution</h2><p>The integration of Artificial Intelligence (AI) and Big Data is setting the stage for the next evolution in Geographic Information Systems (GIS). Traditionally, GIS has been a powerful tool for visualizing and analyzing spatial data, but the sheer volume and velocity of modern data streams—from satellite constellations to IoT sensors—require more advanced techniques.</p><p>This is where AI, particularly machine learning, comes in. By leveraging algorithms that can learn from data, we can automate complex tasks like land cover classification from high-resolution imagery with unprecedented accuracy. Furthermore, predictive models can now forecast urban growth, model climate change impacts, and optimize logistics with a level of detail that was previously unimaginable. As we move forward, the synergy between GIS, AI, and Big Data will not only enhance our understanding of the world but also provide the tools to build a more sustainable and efficient future.</p>',
'https://picsum.photos/seed/blog1/800/450', '2023-10-26', 'Jane Doe', 'Technical Analysis', 'cover', NULL),

('A Deep Dive into NDVI', 'A beginner-friendly guide to understanding the Normalized Difference Vegetation Index (NDVI), its calculation, and its vast applications in agriculture and environmental science.',
'The Normalized Difference Vegetation Index (NDVI) is one of the most widely used indices in remote sensing for a reason: it provides a simple yet powerful measure of vegetation health and density. Calculated from the red and near-infrared (NIR) bands of satellite imagery, the formula is (NIR - Red) / (NIR + Red). Healthy vegetation reflects more NIR light and absorbs more red light, resulting in high NDVI values. Conversely, unhealthy or sparse vegetation yields lower values. This simple metric has profound applications, from precision agriculture where farmers can monitor crop health and apply fertilizers more efficiently, to large-scale environmental monitoring for tracking deforestation, drought, and the impacts of climate change. Understanding NDVI is a fundamental step for anyone looking to harness the power of satellite data for environmental and agricultural analysis.',
'https://picsum.photos/seed/blog2/800/450', '2023-09-15', 'Jane Doe', 'Case Studies', 'cover', 'Global');

-- Insert profile data
INSERT INTO profile_data (name, title, summary, bio, avatar_url, expertise_title, expertise_description, resume_url) VALUES
('Jane Doe', 'GIS & Remote Sensing Specialist | Researcher', 
'A passionate geospatial professional with over 8 years of experience in leveraging GIS and remote sensing technologies to solve complex environmental and urban challenges.',
'A passionate geospatial professional with over 8 years of experience in leveraging GIS and remote sensing technologies to solve complex environmental and urban challenges. My work focuses on the intersection of data science, machine learning, and satellite imagery analysis to derive actionable insights. I am dedicated to pushing the boundaries of what is possible in the world of geospatial intelligence.',
'https://picsum.photos/seed/avatar/400/400', 'My Expertise',
'I possess a wide range of skills across the full spectrum of geospatial technology, from data acquisition and processing to advanced analysis and visualization.',
'#');

-- Insert what I do items
INSERT INTO what_i_do (title, description, sort_order) VALUES
('Spatial Analysis & Modeling', 'Uncovering deep patterns and trends in geographic data to support data-driven decision-making and strategic planning.', 1),
('Advanced Remote Sensing', 'Expertly analyzing satellite and aerial imagery for large-scale environmental monitoring and precise land-use classification.', 2),
('Geospatial Web Development', 'Building custom, interactive maps and data-rich dashboards that bring complex spatial information to life on the web.', 3);

-- Insert core competencies
INSERT INTO core_competencies (name, sort_order) VALUES
('ArcGIS Pro', 1), ('QGIS', 2), ('Python (ArcPy, GDAL)', 3), ('Multispectral Analysis', 4),
('Machine Learning', 5), ('PostGIS', 6), ('Google Earth Engine', 7), ('LiDAR Processing', 8);

-- Insert education
INSERT INTO education (degree, institution, period, sort_order) VALUES
('Ph.D. in Geospatial Science', 'University of GeoTech', '2014 - 2018', 1),
('M.S. in Geographic Information Science', 'State University of Cartography', '2012 - 2014', 2),
('B.S. in Environmental Science', 'Global College', '2008 - 2012', 3);

-- Insert experience
INSERT INTO experience (role, company, period, description, sort_order) VALUES
('Senior Geospatial Data Scientist', 'GeoInnovate Inc.', '2020 - Present', 
'Leading research and development of ML models for satellite image analysis. Developed automated workflows for environmental monitoring projects, significantly improving efficiency.', 1),
('GIS Analyst', 'EnviroConsult Group', '2018 - 2020', 
'Performed spatial analysis, data management, and map production for various environmental impact assessment projects. Specialized in habitat suitability modeling and hydrological analysis.', 2);

-- Insert certifications
INSERT INTO certifications (name, issuer, date, sort_order) VALUES
('Certified GIS Professional (GISP)', 'GIS Certification Institute', '2019', 1),
('Google Certified Professional - Data Engineer', 'Google Cloud', '2021', 2);

-- Insert training
INSERT INTO training (name, institution, year, sort_order) VALUES
('Advanced Python for Geospatial Data Science', 'DataCamp', '2022', 1),
('Deep Learning for Remote Sensing', 'Coursera', '2021', 2);

-- Insert memberships
INSERT INTO memberships (name, period, sort_order) VALUES
('American Society for Photogrammetry and Remote Sensing (ASPRS)', '2018 - Present', 1),
('Urban and Regional Information Systems Association (URISA)', '2019 - Present', 2);

-- Insert skills
INSERT INTO skills (category, sort_order) VALUES
('GIS Software & Platforms', 1),
('Remote Sensing & Image Analysis', 2),
('Programming & Data Science', 3),
('Web & Database Management', 4);

-- Insert skill items
INSERT INTO skill_items (skill_id, name, percentage, sort_order) VALUES
(1, 'ArcGIS Pro', 95, 1), (1, 'QGIS', 90, 2), (1, 'ArcGIS Online', 85, 3), (1, 'PostGIS', 80, 4), (1, 'Google Earth Engine', 75, 5),
(2, 'ENVI', 90, 1), (2, 'Erdas Imagine', 85, 2), (2, 'SNAP', 80, 3), (2, 'Multispectral Analysis', 95, 4), (2, 'LiDAR Processing', 88, 5),
(3, 'Python (ArcPy, GDAL)', 95, 1), (3, 'R', 75, 2), (3, 'SQL', 85, 3), (3, 'Jupyter Notebooks', 90, 4), (3, 'Machine Learning', 80, 5),
(4, 'GeoServer', 70, 1), (4, 'PostgreSQL', 80, 2), (4, 'HTML/CSS', 90, 3), (4, 'JavaScript', 85, 4), (4, 'React', 80, 5);

-- Insert site settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_title', 'GeoPortfolio'),
('site_description', 'A modern, dynamic-style personal portfolio website template for GIS & Remote Sensing professionals.'),
('favicon_url', 'https://www.google.com/s2/favicons?sz=64&domain=react.dev'),
('copyright_text', 'Jane Doe. All Rights Reserved.'),
('twitter_url', 'https://twitter.com'),
('github_url', 'https://github.com'),
('linkedin_url', 'https://linkedin.com');

-- Insert contact info
INSERT INTO contact_info (email, phone, address) VALUES
('jane.doe@example.com', '+1 (555) 123-4567', '123 Geospatial Lane, Mapville, ST 12345');

-- Insert email settings
INSERT INTO email_settings (smtp_server, smtp_port, smtp_user, smtp_pass, from_name, from_email) VALUES
('smtp.example.com', 587, 'user@example.com', '', 'GeoPortfolio Pro', 'noreply@geoportfolio.com');