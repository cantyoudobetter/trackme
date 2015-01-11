//
//  trackmeViewController.m
//  trackme
//
//  Created by Michael Bordelon on 11/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import "trackmeViewController.h"

@implementation trackmeViewController

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad
{
    if (![CLLocationManager significantLocationChangeMonitoringAvailable]) 
    {UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Sorry" message:@"Your device won't support the significant location change." delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alert show];
        [alert release];
        return YES;                                  
    }                                            
    [self initLocationManager];
    return YES;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}


-(void)initLocationManager {                         
    if (locationManager == nil) {
        locationManager = [[CLLocationManager alloc] init];
        locationManager.delegate = self;
        [locationManager startMonitoringSignificantLocationChanges];
    }
}
- (void)saveCurrentData:(NSString *)newData {               
    NSLog(@"%@",newData);
    NSString * u = [NSString stringWithFormat:@"http://bordeloniphone.com/track/up.php?l=12345"];
    NSLog(@"%@", u);
    NSURL *url = [NSURL URLWithString:u];
    ASIHTTPRequest *request = [ASIHTTPRequest requestWithURL:url];
    [request setValidatesSecureCertificate:NO];
    [request addRequestHeader:@"User-Agent" value:@"ASIHTTPRequest"]; 
    [request startSynchronous];
    NSError *error = [request error];
        NSString *response = [request responseString];
        NSLog(@"RESPONSE:%@", response);
        
    
    /*
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    NSMutableArray *savedData = [[NSMutableArray alloc] initWithArray:[defaults objectForKey:@"kLocationData"]];
    [savedData addObject:newData];
    [defaults setObject:savedData forKey:@"kLocationData"];
    [savedData release];
    */ 
}


- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation {                                        
    NSString *lon = [NSString stringWithFormat:@"%.6f", newLocation.coordinate.longitude];
    NSString *lat = [NSString stringWithFormat:@"%.6f",newLocation.coordinate.latitude];
    //[self saveCurrentData:locationData];
    NSString * u = [NSString stringWithFormat:@"http://bordeloniphone.com/track/up.php?lat=%@&lon=%@",lat,lon];
    NSLog(@"%@", u);
    NSURL *url = [NSURL URLWithString:u];
    ASIHTTPRequest *request = [ASIHTTPRequest requestWithURL:url];
    [request setValidatesSecureCertificate:NO];
    [request addRequestHeader:@"User-Agent" value:@"ASIHTTPRequest"]; 
    [request startSynchronous];
    NSError *error = [request error];
    NSString *response = [request responseString];
    NSLog(@"RESPONSE:%@", response);
    
    
}                                                  

- (void)locationManager:(CLLocationManager *)manager didFailWithError:(NSError *)error {                         
    NSString *errorData = [NSString stringWithFormat:@"%@",[error localizedDescription]];
    NSLog(@"%@", errorData);                              
}
- (void)dealloc {
    [[NSNotificationCenter defaultCenter] removeObserver:self];
    [locationManager release];
    [super dealloc];
}

@end
