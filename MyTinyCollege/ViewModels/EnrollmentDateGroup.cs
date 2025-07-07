using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Web;

namespace MyTinyCollege.ViewModels
{
    public class EnrollmentDateGroup
    {
        // This will be used to show student body stats Counting How many students enrolled on a particular EnrollmentDate

        // Without the annotation we would get a date time
        // 9/1/2016 12:00:00 AM

        [DataType(DataType.Date)]
        public DateTime? EnrollmentDate { get; set; }
        public int StudentCount { get; set; }
    }
}