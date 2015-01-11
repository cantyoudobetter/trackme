//
//  trackmeViewController.h
//  trackme
//
//  Created by Michael Bordelon on 11/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <CoreLocation/CoreLocation.h>
#import "ASIHTTPRequest.h"

@interface trackmeViewController : UIViewController <CLLocationManagerDelegate>
{
    CLLocationManager *locationManager;
}
    
@end
