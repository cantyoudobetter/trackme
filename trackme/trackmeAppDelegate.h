//
//  trackmeAppDelegate.h
//  trackme
//
//  Created by Michael Bordelon on 11/1/11.
//  Copyright 2011 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@class trackmeViewController;

@interface trackmeAppDelegate : NSObject <UIApplicationDelegate>

@property (nonatomic, retain) IBOutlet UIWindow *window;

@property (nonatomic, retain) IBOutlet trackmeViewController *viewController;

@end
